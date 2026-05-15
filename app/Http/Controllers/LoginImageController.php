<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;

class LoginImageController extends Controller
{
    public function index()
    {
        $pageTitle = __('messages.login_image');
        $auth_user = authSession();
        $app_setting = AppSetting::first();
        if (!$app_setting) {
            $app_setting = new AppSetting();
            $app_setting->save();
        }
        $login_image = getSingleMedia($app_setting, 'login_image');
        
        return view('login_image.form', compact('pageTitle', 'auth_user', 'app_setting', 'login_image'));
    }

    public function store(Request $request)
    {
        $app_setting = AppSetting::first();
        if (!$app_setting) {
            $app_setting = new AppSetting();
            $app_setting->save();
        }

        if ($request->hasFile('login_image')) {
            $app_setting->clearMediaCollection('login_image');
            $app_setting->addMediaFromRequest('login_image')->toMediaCollection('login_image');
        }

        return redirect()->back()->withSuccess(__('messages.update_form', ['form' => __('messages.login_image')]));
    }
}
