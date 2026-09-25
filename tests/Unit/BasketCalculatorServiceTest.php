<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\BasketCalculatorService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BasketCalculatorServiceTest extends TestCase
{
    protected function makeBasket(array $products = []): BasketCalculatorService
    {
        $products = $products ?: [
            ['code' => 'R01', 'name' => 'Red Widget', 'price' => 3295],
            ['code' => 'G01', 'name' => 'Green Widget', 'price' => 2495],
            ['code' => 'B01', 'name' => 'Blue Widget', 'price' => 795],
        ];

        return new BasketCalculatorService(collect($products)->map(fn (array $attributes) => new Product($attributes)));
    }

    public static function exampleBaskets(): array
    {
        return [
            'R01, R01' => [['R01', 'R01'], 5437],
            'R01, G01' => [['R01', 'G01'], 6085],
            'B01, G01' => [['B01', 'G01'], 3785],
            'B01, B01, R01, R01, R01' => [['B01', 'B01', 'R01', 'R01', 'R01'], 9827],
        ];
    }

    #[DataProvider('exampleBaskets')]
    public function test_example_basket_totals(array $codes, int $expectedTotal): void
    {
        $basket = $this->makeBasket();

        foreach ($codes as $code) {
            $basket->add($code);
        }

        $this->assertSame($expectedTotal, $basket->total());
    }

    public static function deliveryTiers(): array
    {
        return [
            'just under $50' => [4999, 495],
            'exactly $50' => [5000, 295],
            'just under $90' => [8999, 295],
            'exactly $90' => [9000, 0],
        ];
    }

    #[DataProvider('deliveryTiers')]
    public function test_delivery_charge(int $price, int $expectedDelivery): void
    {
        $basket = $this->makeBasket([['code' => 'X01', 'name' => 'Widget', 'price' => $price]]);
        $basket->add('X01');

        $this->assertSame($expectedDelivery, $basket->delivery());
    }

    public static function redWidgetQuantities(): array
    {
        return [
            'one' => [1, 0],
            'two' => [2, 1648],
            'three' => [3, 1648],
            'four' => [4, 3296],
        ];
    }

    #[DataProvider('redWidgetQuantities')]
    public function test_every_second_red_widget_is_half_price(int $quantity, int $expectedDiscount): void
    {
        $basket = $this->makeBasket();

        for ($i = 0; $i < $quantity; $i++) {
            $basket->add('R01');
        }

        $this->assertSame($expectedDiscount, $basket->discount());
    }

    public function test_other_products_are_not_discounted(): void
    {
        $basket = $this->makeBasket();
        $basket->add('G01');
        $basket->add('G01');

        $this->assertSame(0, $basket->discount());
    }

    public function test_it_groups_items_by_product(): void
    {
        $basket = $this->makeBasket();
        $basket->add('G01');
        $basket->add('B01');
        $basket->add('G01');

        $items = $basket->items();

        $this->assertSame(['G01', 'B01'], array_map(fn (array $item) => $item['product']->code, $items));
        $this->assertSame([2, 1], array_column($items, 'quantity'));
    }

    public function test_empty_basket_costs_nothing(): void
    {
        $basket = $this->makeBasket();

        $this->assertSame([], $basket->items());
        $this->assertSame(0, $basket->delivery());
        $this->assertSame(0, $basket->total());
    }

    public function test_adding_an_unknown_product_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->makeBasket()->add('X01');
    }
}
