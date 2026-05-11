<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Http\Resources\API\SubCategoryResource;
use App\Traits\ZoneTrait;


class SubCategoryController extends Controller
{
    use ZoneTrait;
    public function getSubCategoryList(Request $request){
        $subcategory = SubCategory::where('status',1);
        if(auth()->user() !== null){
            if(auth()->user()->hasRole('admin')){
                $subcategory = new SubCategory();
                $subcategory = $subcategory->withTrashed();
            }
        }
        if($request->has('is_featured')){
            $subcategory->where('is_featured',$request->is_featured);
        }
        if($request->has('category_id')){
            $subcategory->where('category_id',$request->category_id);
        }

        if ($request->has('latitude') && $request->has('longitude')) {
            $zoneIds = $this->getMatchingZonesByLatLng($request->latitude, $request->longitude);
            if (!empty($zoneIds)) {
                $subcategory->whereHas('services', function($q) use ($zoneIds) {
                    $q->where('status', 1)
                      ->whereHas('zones', function($q2) use ($zoneIds) {
                          $q2->whereIn('service_zones.id', $zoneIds);
                      });
                });
            } else {
                $subcategory->whereRaw('1 = 0');
            }
        }
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page)){
                $per_page = $request->per_page;
            }
            if($request->per_page === 'all' ){
                $per_page = $subcategory->count();
            }
        }

        $subcategory = $subcategory->orderBy('name','asc')->paginate($per_page);
        $items = SubCategoryResource::collection($subcategory);
        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
        ];
        
        return comman_custom_response($response);
    }

}