<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;

class OfferController extends Controller
{
    // GET /api/offers
    public function index($lang)
    {
            // Default to English if unsupported language
    if (!in_array($lang, ['en', 'kn'])) {
        return response()->json(['error' => 'Language not supported'], 400);
    }

    // Fetch offers with translations
    $offers = Offer::with('translations')->get()->map(function ($offer) use ($lang) {
        $translation = $offer->translations->where('locale', $lang)->first();

        return [
            'id'     => $offer->id,
            'name'   => $offer->name,
            'title'  => $translation?->title ?? null,
            'text'   => $translation?->text ?? null,
            'image_url' => $offer->image_url,
            'active'   => $offer->active,
        ];
    });

    return response()->json($offers);
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
