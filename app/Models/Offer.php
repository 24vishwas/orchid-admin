<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Offer extends Model
{
    use HasFactory, AsSource;

     protected $fillable = [
        'name',
        'title',
        'text',
        'image_url',
        'active',
    ];
    public function translations()
    {
        return $this->hasMany(OfferTranslation::class);
    }

    public function getTitle($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()
            ->where('locale', $locale)
            ->first()
            ->title ?? null;
    }
}
