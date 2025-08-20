<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    //
    public function index($lang)
    {

        if (!in_array($lang, ['en', 'kn'])) {
            return response()->json(['error' => 'Language not supported'], 400);
        }
        $categories = ServiceCategory::with('translations')->get();
        return ServiceCategoryResource::collection($categories);
    }
}
