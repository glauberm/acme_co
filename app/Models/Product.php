<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['code', 'name', 'price'];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
        ];
    }

    public function basketItem(): HasOne
    {
        return $this->hasOne(BasketItem::class);
    }
}
