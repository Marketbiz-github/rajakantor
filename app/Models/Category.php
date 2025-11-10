<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'id_category',
        'name',
        'slug',
        'description',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    public function parent(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_parents', 'id_category', 'id_parent')
            ->where('category_parents.status', '1');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_parents', 'id_parent', 'id_category')
            ->where('category_parents.status', '1');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories', 'id_category', 'id_product');
    }
}