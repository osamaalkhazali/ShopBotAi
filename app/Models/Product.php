<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;


class Product extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'asin',
        'title',
        'imgUrl',
        'productURL',
        'stars',
        'reviews',
        'price',
        'listPrice',
        'category_id',
        'isBestSeller',
        'boughtInLastMonth'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function savedProducts()
    {
        return $this->hasMany(SavedProduct::class);
    }

    public function productViews()
    {
        return $this->hasMany(ProductView::class);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'asin' => $this->asin,
            'title' => $this->title,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'stars' => $this->stars,
            'reviews' => $this->reviews,
            'isBestSeller' => $this->isBestSeller,
        ];
    }
}
