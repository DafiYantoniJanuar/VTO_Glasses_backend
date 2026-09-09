<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'shape',
        'color',
        'price',
        'category',
        'description',
        'image',
        'model_3d_url',
        'stock',
        'best_seller',
        'rating',
        'reviews',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'best_seller' => 'boolean',
        'rating' => 'float',
        'reviews' => 'integer',
    ];

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function productReviews()
    {
        return $this->hasMany(Review::class);
    }
}
