<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfferForYou;
use Yajra\DataTables\DataTables;

class OfferForYouController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $pageTitle = trans('messages.list_form_title', ['form' => trans('messages.offers_for_you')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('offers_for_you.index', compact('pageTitle', 'auth_user', 'assets', 'filter'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = OfferForYou::query();
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" data-type="offer" onclick="dataTableRowCheck(' . $row->id . ',this)">';
            })
            ->editColumn('title', function ($query) {
                $link = '<a class="btn-link btn-link-hover" href=' . route('offers-for-you.create', ['id' => $query->id]) . '>' . $query->title . '</a>';
                return $link;
            })
            ->editColumn('status', function ($query) {
                $disabled = $query->deleted_at ? 'disabled' : '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input bg-primary change_status" ' . $disabled . ' data-type="offer_status" ' . ($query->status ? "checked" : "") . ' value="' . $query->id . '" id="' . $query->id . '" data-id="' . $query->id . '">
                        <label class="custom-control-label" for="' . $query->id . '" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->addColumn('action', function ($offer) {
                return view('offers_for_you.action', compact('offer'))->render();
            })
            ->addIndexColumn()
            ->rawColumns(['title', 'action', 'status', 'check'])
            ->toJson();
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $message = 'Bulk Action Updated';

        switch ($actionType) {
            case 'change-status':
                OfferForYou::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk Offer Status Updated';
                break;

            case 'delete':
                OfferForYou::whereIn('id', $ids)->delete();
                $message = 'Bulk Offer Deleted';
                break;

            case 'restore':
                OfferForYou::whereIn('id', $ids)->restore();
                $message = 'Bulk Offer Restored';
                break;

            case 'permanently-delete':
                OfferForYou::whereIn('id', $ids)->forceDelete();
                $message = 'Bulk Offer Permanently Deleted';
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Action Invalid']);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // if (!auth()->user()->can('offer add')) {
        //     return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        // }
        $pageTitle1 = trans('messages.setting');
        $page = 'offers-for-you';
        $id = $request->id;
        $auth_user = authSession();

        $offerdata = OfferForYou::find($id);
        $pageTitle = trans('messages.update_form_title', ['form' => trans('messages.offers_for_you')]);

        if ($offerdata == null) {
            $pageTitle = trans('messages.add_button_form', ['form' => trans('messages.offers_for_you')]);
            $offerdata = new OfferForYou;
        }

        return view('offers_for_you.create', compact('pageTitle', 'offerdata', 'auth_user', 'pageTitle1', 'page'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if (demoUserPermission()) {
        //     return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        // }

        $data = $request->all();
        $data['id'] = $request->id;

        $result = OfferForYou::updateOrCreate(['id' => $request->id], $data);

        storeMediaFile($result, $request->offer_image, 'offer_image');

        $message = __('messages.update_form', ['form' => __('messages.offers_for_you')]);
        if ($result->wasRecentlyCreated) {
            $message = __('messages.save_form', ['form' => __('messages.offers_for_you')]);
        }

        if ($request->is('api/*')) {
            return comman_message_response($message);
        }
        return redirect(route('offers-for-you.index'))->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // if (demoUserPermission()) {
        //     return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        // }
        $offer = OfferForYou::find($id);
        $msg = __('messages.msg_fail_to_delete', ['item' => __('messages.offers_for_you')]);

        if ($offer != '') {
            $offer->delete();
            $msg = __('messages.msg_deleted', ['name' => __('messages.offers_for_you')]);
        }
        return comman_custom_response(['message' => $msg, 'status' => true]);
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $offer = OfferForYou::withTrashed()->where('id', $id)->first();
        $msg = __('messages.not_found_entry', ['name' => __('messages.offers_for_you')]);

        if ($request->type == 'restore') {
            $offer->restore();
            $msg = __('messages.msg_restored', ['name' => __('messages.offers_for_you')]);
        }

        if ($request->type === 'forcedelete') {
            $offer->forceDelete();
            $msg = __('messages.msg_forcedelete', ['name' => __('messages.offers_for_you')]);
        }
        return comman_custom_response(['message' => $msg, 'status' => true]);
    }
}
