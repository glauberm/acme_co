<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'items' => array_map(fn (array $item) => [
                'code' => $item['product']->code,
                'name' => $item['product']->name,
                'price' => $item['product']->price,
                'quantity' => $item['quantity'],
            ], $this->items()),
            'subtotal' => $this->subtotal(),
            'discount' => $this->discount(),
            'delivery' => $this->delivery(),
            'total' => $this->total(),
        ];
    }
}
