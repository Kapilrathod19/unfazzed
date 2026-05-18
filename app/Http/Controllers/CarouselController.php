<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\AppSetting;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CarouselController extends Controller
{
    public function index()
    {
        $pageTitle = 'Carousel Image';
        $auth_user = authSession();
        $app_setting = AppSetting::first();
        if (!$app_setting) {
            $app_setting = new AppSetting();
            $app_setting->save();
        }
        $carousel_images = $app_setting->getMedia('carousel_image');
        
        return view('carousel.form', compact('pageTitle', 'auth_user', 'app_setting', 'carousel_images'));
    }

    public function store(Request $request)
    {
        $app_setting = AppSetting::first();
        if (!$app_setting) {
            $app_setting = new AppSetting();
            $app_setting->save();
        }

        if ($request->hasFile('carousel_image')) {
            foreach ($request->file('carousel_image') as $file) {
                $app_setting->addMedia($file)->toMediaCollection('carousel_image');
            }
        }

        return redirect()->back()->withSuccess(__('messages.update_form', ['form' => __('messages.carousel_image') ?? 'Carousel Image']));
    }

    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        $media->delete();
        return redirect()->back()->withSuccess(__('messages.delete_form', ['form' => __('messages.carousel_image') ?? 'Carousel Image']));
    }
}
