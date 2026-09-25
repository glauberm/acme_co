<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddBasketItemRequest;
use App\Http\Resources\BasketResource;
use App\Services\BasketService;

class BasketController extends Controller
{
    public function __construct(protected BasketService $baskets) {}

    public function show(): BasketResource
    {
        return new BasketResource($this->baskets->current());
    }

    public function store(AddBasketItemRequest $request): BasketResource
    {
        return new BasketResource($this->baskets->add($request->validated('code')));
    }

    public function destroyItem(string $code): BasketResource
    {
        return new BasketResource($this->baskets->remove($code));
    }

    public function destroy(): BasketResource
    {
        return new BasketResource($this->baskets->clear());
    }
}
