<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderChangeRequest;
use App\Models\User;
use App\Models\Category;
use App\Models\ServiceZone;
use Yajra\DataTables\DataTables;

class ProviderChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('messages.provider_change_requests');
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('provider_change_request.index', compact('pageTitle', 'auth_user', 'assets'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = ProviderChangeRequest::with('provider')->where('status', 'pending');

        return $datatable->eloquent($query)
            ->editColumn('provider_id', function ($row) {
                return $row->provider ? $row->provider->display_name : '-';
            })
            ->editColumn('type', function ($row) {
                return ucfirst($row->type);
            })
            ->editColumn('value', function ($row) {
                $ids = is_array($row->value) ? $row->value : json_decode($row->value, true);
                if ($row->type == 'category') {
                    return Category::whereIn('id', $ids)->pluck('name')->implode(', ');
                } else {
                    return ServiceZone::whereIn('id', $ids)->pluck('name')->implode(', ');
                }
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('provider-change-request.approve', $row->id) . '" class="btn btn-sm btn-success mr-1"><i class="fa fa-check"></i> ' . __('messages.approve') . '</a>' .
                       '<a href="' . route('provider-change-request.reject', $row->id) . '" class="btn btn-sm btn-danger"><i class="fa fa-times"></i> ' . __('messages.reject') . '</a>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function approve($id)
    {
        $request = ProviderChangeRequest::findOrFail($id);
        $provider = User::findOrFail($request->provider_id);

        $ids = is_array($request->value) ? $request->value : json_decode($request->value, true);

        if ($request->type == 'category') {
            $provider->categories()->syncWithoutDetaching($ids);
        } elseif ($request->type == 'zone') {
            $provider->zones()->syncWithoutDetaching($ids);
        }

        $request->status = 'approved';
        $request->save();

        return redirect()->back()->withSuccess(__('messages.approve_successfully'));
    }

    public function reject($id)
    {
        $request = ProviderChangeRequest::findOrFail($id);
        $request->status = 'rejected';
        $request->save();

        return redirect()->back()->withSuccess(__('messages.rejected_successfully'));
    }
}
