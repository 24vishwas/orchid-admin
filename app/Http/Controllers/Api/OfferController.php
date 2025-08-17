<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;

class OfferController extends Controller
{
    // GET /api/offers
    public function index()
    {
        return response()->json(Offer::all());
    }
     // POST /api/offers
     public function store(Request $request)
     {
         $data = $request->validate([
             'name'      => 'required|string|max:255',
             'title'     => 'required|string|max:255',
             'text'      => 'nullable|string',
             'image_url' => 'nullable|url',
             'active'    => 'boolean'
         ]);
 
         $offer = Offer::create($data);
 
         return response()->json($offer, 201);
     }
 
}
