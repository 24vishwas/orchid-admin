<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ServiceCategory extends Model
{
    //
    use HasFactory, AsSource;

    protected $fillable = [
        'title',
        'image_path',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(ServiceCategoryTranslation::class);
    }


    /**
     * Get the translation for the given locale, or the default locale if no translation is found.
     *
     * @param string|null $locale The locale to get the translation for. Defaults to the current locale.
     * @return string|null The translation for the given locale, or null if no translation is found.
     */
    public function getTitle($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()
            ->where('locale', $locale)
            ->first()
            ->title ?? null;
    }

}
