<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    //
    public function index()
    {
        return ServiceCategoryResource::collection(
            ServiceCategory::where('active', true)->orderBy('title')->get()
        );
    }
}
