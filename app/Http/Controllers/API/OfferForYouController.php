<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfferForYou;
use App\Http\Resources\API\OfferForYouResource;

class OfferForYouController extends Controller
{
    public function getOfferList(Request $request)
    {
        $offers = OfferForYou::where('status', 1);

        $per_page = $request->input('per_page', 10);
        $offers = $offers->paginate($per_page);

        $items = OfferForYouResource::collection($offers);

        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
            'data' => $items,
        ];

        return comman_custom_response($response);
    }
}
