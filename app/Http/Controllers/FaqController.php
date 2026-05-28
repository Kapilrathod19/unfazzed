<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use Yajra\DataTables\DataTables;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = trans('messages.faqs');
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('faq.index', compact('pageTitle', 'auth_user', 'assets'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = Faq::query();

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-'.$row->id.'" name="datatable_ids[]" value="'.$row->id.'" data-type="faq" onclick="dataTableRowCheck('.$row->id.',this)">';
            })
            ->editColumn('status', function ($row) {
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input change_status" data-type="faq_status" '.($row->status ? "checked" : "").' value="'.$row->id.'" id="'.$row->id.'" data-id="'.$row->id.'">
                        <label class="custom-control-label" for="'.$row->id.'" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->addColumn('action', function($faq){
                return view('faq.action', compact('faq'))->render();
            })
            ->addIndexColumn()
            ->rawColumns(['check', 'action', 'status'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $id = $request->id;
        $auth_user = authSession();
        $faqdata = Faq::find($id);
        $pageTitle = trans('messages.update_form_title', ['form' => trans('messages.faq')]);

        if ($faqdata == null) {
            $pageTitle = trans('messages.add_button_form', ['form' => trans('messages.faq')]);
            $faqdata = new Faq;
        }

        return view('faq.create', compact('pageTitle', 'faqdata', 'auth_user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (demoUserPermission()) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }

        $request->validate([
            'title' => 'required|max:100',
            'description' => 'required',
            'status' => 'required'
        ]);

        $data = $request->all();
        $result = Faq::updateOrCreate(['id' => $data['id']], $data);

        $message = trans('messages.update_form', ['form' => trans('messages.faq')]);
        if ($result->wasRecentlyCreated) {
            $message = trans('messages.save_form', ['form' => trans('messages.faq')]);
        }

        return redirect(route('faq.index'))->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        if (demoUserPermission()) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }

        $faq = Faq::find($id);
        $msg = __('messages.msg_fail_to_delete', ['name' => __('messages.faq')]);

        if ($faq != '') {
            $faq->delete();
            $msg = __('messages.msg_deleted', ['name' => __('messages.faq')]);
        }

        return redirect()->back()->withSuccess($msg);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        $message = 'Bulk Action Updated';
        switch ($actionType) {
            case 'change-status':
                Faq::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk FAQ Status Updated';
                break;

            case 'delete':
                Faq::whereIn('id', $ids)->delete();
                $message = 'Bulk FAQ Deleted';
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Action Invalid']);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $faq = Faq::where('id', $id)->first();
        $msg = __('messages.not_found_entry', ['name' => __('messages.faq')]);

        if ($faq != null) {
            if ($request->type === 'forcedelete') {
                $faq->forceDelete();
                $msg = __('messages.msg_forcedelete', ['name' => __('messages.faq')]);
            }
        }

        return comman_custom_response(['message' => $msg, 'status' => true]);
    }
}
