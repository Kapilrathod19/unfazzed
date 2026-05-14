<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderPayout;
use App\Models\Booking;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Bank;
use Carbon\Carbon;
use App\Http\Requests\ProviderPayout as ProviderPayoutRequest;
use Yajra\DataTables\DataTables;
use App\Traits\NotificationTrait;
use App\Traits\EarningTrait;
use App\Models\CommissionEarning;
use App\Models\Setting;
class ProviderPayoutController extends Controller
{
    use NotificationTrait;
    use EarningTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function index_data(DataTables $datatable,Request $request)
    {
        $id = $request->id;
        $query = ProviderPayout::where('provider_id',$id);
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->newquery();
        }

        return $datatable->eloquent($query)
        ->addColumn('check', function ($row) {
            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
        })
        ->editColumn('payment_method', function($payout) {
            return !empty($payout->payment_method) ? ucfirst($payout->payment_method) : 'cash';
        })
        ->addColumn('bank_name', function($payout) {

            if($payout->payment_method == 'bank'){
                $bank = Bank::where('id',$payout->bank_id)->value('bank_name');
                return $bank;
            }
            else{
                return '-';
            }

            })
        ->editColumn('description', function($payout) {
            return !empty($payout->description) ? $payout->description : '-';
        })

        ->editColumn('provider_id', function ($payout) {
            return view('providerpayout.user', compact('payout'));
        })

        ->filterColumn('provider_id',function($payout,$keyword){
            $payout->whereHas('providers',function ($q) use($keyword){
                $q->where('first_name','like','%'.$keyword.'%');
            });
        })
        ->editColumn('amount', function($payout) {
            return ($payout->amount != null && isset($payout->amount)) ? getPriceFormat($payout->amount) : '-';
        })
        ->editColumn('created_at', function($payout) {
            return $payout->created_at;
        })
        ->addColumn('action', function($providerpayout){
            return view('providerpayout.action',compact('providerpayout'))->render();
        })
        ->addIndexColumn()
        ->rawColumns(['check','title','action','status','bank_name'])
            ->toJson();
    }

    /* bulck action method */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = 'Bulk Action Updated';

        switch ($actionType) {
            case 'change-status':
                $branches = ProviderPayout::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk Provider Payout Status Updated';
                break;

            case 'delete':
                ProviderPayout::whereIn('id', $ids)->delete();
                $message = 'Bulk Provider Payout Deleted';
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Action Invalid']);
                break;
        }

        return response()->json(['status' => true, 'message' =>$message]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id,$redirect_type = null)
    {
        $auth_user = authSession();
        $pageTitle = trans('messages.add_button_form',['form' => trans('messages.provider_payout')]);
        $payoutdata = new ProviderPayout;

        $provider = User::find($id);

        // Calculate Total Provider Earnings (from completed bookings)
        $total_provider_earning = CommissionEarning::where('employee_id', $id)
            ->whereHas('getbooking', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereIn('user_type', ['provider', 'handyman'])
            ->sum('commission_amount');

        // Calculate Total Paid Amount
        $total_paid_amount = ProviderPayout::where('provider_id', $id)->sum('amount');

        // Due Amount = Total Earning - Total Paid
        $due_amount = $total_provider_earning - $total_paid_amount;

        $setting = Setting::getValueByKey('site-setup','site-setup');
        $digitafter_decimal_point = $setting ? $setting->digitafter_decimal_point : "2";
        
        $payoutdata->amount = round($due_amount, $digitafter_decimal_point);
        $payoutdata->provider_id = $id;

        return view('providerpayout.create', compact('pageTitle' ,'payoutdata' ,'auth_user' ,'redirect_type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  
  public function store(ProviderPayoutRequest $request)
{
    if (demoUserPermission()) {
        return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
    }

    $data = $request->except('_token');
    $provider_id = $data['provider_id'];

    $data['status']    = 'paid';
    $data['paid_date'] = Carbon::now();

    /* ================= CREATE PAYOUT ENTRY ================= */
    $result = ProviderPayout::create($data);

    /* ================= WALLET DEBIT LOGIC ================= */
    $wallet = Wallet::where('user_id', $provider_id)->first();
    if ($wallet) {
        $wallet->amount -= $result->amount;
        $wallet->save();

        // Record history for the debit
        $historyData = [
            'user_id' => $provider_id,
            'activity_type' => 'payout_debit',
            'activity_message' => __('messages.payout_debit_message', ['id' => $result->id, 'amount' => getPriceFormat($result->amount)]),
            'datetime' => date('Y-m-d H:i:s'),
            'activity_data' => json_encode([
                'payout_id' => $result->id,
                'amount' => $result->amount,
                'transaction_type' => 'Debit'
            ])
        ];
        \App\Models\WalletHistory::create($historyData);
    }

    if ($request->is('api*')) {
        return comman_message_response(
            __('messages.created_success', ['form' => 'Provider Payout'])
        );
    }

    return redirect()
        ->route('providerpayout.show', $provider_id)
        ->with('success', __('messages.created_success', ['form' => 'Provider Payout']));
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id != auth()->user()->id && !auth()->user()->hasRole(['admin', 'demo_admin'])) {
            return redirect(route('home'))->withErrors(trans('messages.demo_permission_denied'));
        }
        $providerdata = User::where('user_type','provider')->where('id',$id)->first();
        //
        $pageTitle = __('messages.list_form_title',['form' => __('messages.providerpayout_list')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('providerpayout.view', compact('pageTitle','auth_user','assets','id','providerdata'));
    }


    public function ProviderPayout_index_data(DataTables $datatable,$id)
    {
        $query = ProviderPayout::where('provider_id',$id);

        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->newquery();
        }

        return $datatable ->eloquent($query)
        ->editColumn('payment_method', function($payout) {
            return !empty($payout->payment_method) ? $payout->payment_method : 'cash';
        })
        ->addColumn('bank_name', function($payout) {

        if($payout->payment_method == 'bank'){
            $bank = Bank::where('id',$payout->bank_id)->value('bank_name');
            return $bank;
        }
        else{
            return '-';
        }

        })
        ->editColumn('provider_id', function($payout) {
            return ($payout->providers != null && isset($payout->providers)) ? $payout->providers->display_name : '-';
        })
        ->editColumn('amount', function($payout) {
            return ($payout->amount != null && isset($payout->amount)) ? getPriceFormat($payout->amount) : '-';
        })
        ->editColumn('created_at', function($payout) {
            return $payout->created_at;
        })
        ->addIndexColumn()
        ->rawColumns(['bank_name'])
        ->toJson();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $providerpayout = ProviderPayout::find($id);
        $msg= __('messages.msg_fail_to_delete',['item' => __('messages.providerpayout_list')] );

        if($providerpayout != '') {
            $providerpayout->delete();
            $msg= __('messages.msg_deleted',['name' => __('messages.providerpayout_list')] );
        }
        return comman_custom_response(['message'=> $msg, 'status' => true]);
    }

}

