<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'logo',
        'favicon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'about',
        'information',
        'phone',
        'slider',
        'banner_sidebar',
        'banner_home_top',
        'banner_home_bottom',
        'terms',
        'client'
    ];

    protected $casts = [
        'slider' => 'array'
    ];
}