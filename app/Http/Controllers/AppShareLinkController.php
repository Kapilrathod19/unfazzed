<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class AppShareLinkController extends Controller
{
    public function index()
    {
        $pageTitle = __('messages.app_share_link');
        $auth_user = authSession();
        $setting_data = Setting::where('type', 'app_share_link')->where('key', 'app_share_link')->first();
        $app_share_link = '';
        if ($setting_data) {
            $app_share_link = $setting_data->value;
        }
        
        return view('app_share_link.form', compact('pageTitle', 'auth_user', 'app_share_link', 'setting_data'));
    }

    public function store(Request $request)
    {
        $setting_data = [
            'type'  => 'app_share_link',
            'key'   => 'app_share_link',
            'value' => $request->app_share_link,
        ];
        Setting::updateOrCreate(['type' => 'app_share_link', 'key' => 'app_share_link'], $setting_data);

        return redirect()->back()->withSuccess(__('messages.update_form', ['form' => __('messages.app_share_link')]));
    }
}
