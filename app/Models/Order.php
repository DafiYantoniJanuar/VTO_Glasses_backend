<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'address',
        'city',
        'zip',
        'items',
        'subtotal',
        'tax',
        'total',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'float',
        'tax' => 'float',
        'total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
