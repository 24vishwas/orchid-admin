<?php
use App\Http\Controllers\Api\ServiceCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OfferController;

Route::get('/offers', [OfferController::class, 'index']);
Route::post('/offers', [OfferController::class, 'store']);

Route::get('/service-categories', [ServiceCategoryController::class, 'index']);