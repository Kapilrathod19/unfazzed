<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function getFaqList(Request $request)
    {
        $faq = Faq::orderBy('id', 'desc');

        if (!empty($request->status)) {
            $faq = $faq->where('status', $request->status);
        } else {
            $faq = $faq->where('status', 1);
        }

        if (!empty($request->search)) {
            $faq = $faq->where(function($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $faq->count();
            }
        }

        $faq = $faq->paginate($per_page);

        $response = [
            'pagination' => [
                'total_items' => $faq->total(),
                'per_page' => $faq->perPage(),
                'currentPage' => $faq->currentPage(),
                'totalPages' => $faq->lastPage(),
                'from' => $faq->firstItem(),
                'to' => $faq->lastItem(),
                'next_page' => $faq->nextPageUrl(),
                'previous_page' => $faq->previousPageUrl(),
            ],
            'data' => $faq->items(),
        ];

        return comman_custom_response($response);
    }
}
