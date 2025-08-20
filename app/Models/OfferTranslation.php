<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferTranslation extends Model
{
    //
    protected $fillable = ['offer_id', 'locale', 'title'];


    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
