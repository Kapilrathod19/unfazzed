<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\AppSetting;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        $login_images = $app_setting->getMedia('login_image');
        
        return view('login_image.form', compact('pageTitle', 'auth_user', 'app_setting', 'login_images'));
    }

    public function store(Request $request)
    {
        $app_setting = AppSetting::first();
        if (!$app_setting) {
            $app_setting = new AppSetting();
            $app_setting->save();
        }

        if ($request->hasFile('login_image')) {
            foreach ($request->file('login_image') as $file) {
                $app_setting->addMedia($file)->toMediaCollection('login_image');
            }
        }

        return redirect()->back()->withSuccess(__('messages.update_form', ['form' => __('messages.login_image')]));
    }

    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        $media->delete();
        return redirect()->back()->withSuccess(__('messages.delete_form', ['form' => __('messages.login_image')]));
    }
}
