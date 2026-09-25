<?php

namespace App\Services;

use App\Models\BasketItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class BasketService
{
    public function current(): BasketCalculatorService
    {
        $basket = new BasketCalculatorService(Product::all());

        BasketItem::with('product')->orderBy('id')->get()->each(function (BasketItem $item) use ($basket) {
            for ($i = 0; $i < $item->quantity; $i++) {
                $basket->add($item->product->code);
            }
        });

        return $basket;
    }

    public function add(string $code): BasketCalculatorService
    {
        $product = Product::where('code', $code)->firstOrFail();

        BasketItem::upsert(
            [['product_id' => $product->id, 'quantity' => 1]],
            uniqueBy: ['product_id'],
            update: ['quantity' => DB::raw('quantity + 1')],
        );

        return $this->current();
    }

    public function remove(string $code): BasketCalculatorService
    {
        DB::transaction(function () use ($code) {
            $item = BasketItem::whereRelation('product', 'code', $code)->lockForUpdate()->firstOrFail();

            $item->quantity > 1 ? $item->decrement('quantity') : $item->delete();
        });

        return $this->current();
    }

    public function clear(): BasketCalculatorService
    {
        BasketItem::query()->delete();

        return $this->current();
    }
}
