<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategoryTranslation extends Model
{
    //
    protected $fillable = ['service_category_id', 'locale', 'title'];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
