<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'price',
        'status'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'id_product', 'id_category');
    }

    // public function tags(): BelongsToMany
    // {
    //     return $this->belongsToMany(Tag::class, 'product_tags');
    // }
}