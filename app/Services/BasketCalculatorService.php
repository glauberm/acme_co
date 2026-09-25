<?php

namespace App\Services;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class BasketCalculatorService
{
    // All amounts are in cents.
    const DELIVERY_RULES = [
        ['below' => 5000, 'charge' => 495],
        ['below' => 9000, 'charge' => 295],
    ];

    const OFFERS = [
        ['product' => 'R01', 'every' => 2, 'percent_off' => 50],
    ];

    protected Collection $products;

    protected array $quantities = [];

    public function __construct(
        Collection $products,
        protected array $deliveryRules = self::DELIVERY_RULES,
        protected array $offers = self::OFFERS,
    ) {
        $this->products = $products->keyBy('code');
    }

    public function add(string $code): void
    {
        if (! $this->products->has($code)) {
            throw new InvalidArgumentException("Unknown product code [{$code}].");
        }

        $this->quantities[$code] = ($this->quantities[$code] ?? 0) + 1;
    }

    public function items(): array
    {
        return collect($this->quantities)
            ->map(fn (int $quantity, string $code) => ['product' => $this->products[$code], 'quantity' => $quantity])
            ->values()
            ->all();
    }

    public function subtotal(): int
    {
        return collect($this->items())->sum(fn (array $item) => $item['product']->price * $item['quantity']);
    }

    public function discount(): int
    {
        return collect($this->offers)->sum(function (array $offer) {
            $price = $this->products->get($offer['product'])?->price ?? 0;
            $discountedUnits = intdiv($this->quantities[$offer['product']] ?? 0, $offer['every']);

            // The discounted price is rounded down to the cent, as the assignment's example totals require.
            return $discountedUnits * ($price - intdiv($price * (100 - $offer['percent_off']), 100));
        });
    }

    public function delivery(): int
    {
        if (empty($this->quantities)) {
            return 0;
        }

        $amount = $this->subtotal() - $this->discount();

        return collect($this->deliveryRules)->first(fn (array $rule) => $amount < $rule['below'])['charge'] ?? 0;
    }

    public function total(): int
    {
        return $this->subtotal() - $this->discount() + $this->delivery();
    }
}
