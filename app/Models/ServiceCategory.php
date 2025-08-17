<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ServiceCategory extends Model
{
    //
    use AsSource;

    protected $fillable = [
        'title',
        'image_path',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
