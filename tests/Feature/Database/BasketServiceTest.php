<?php

namespace Tests\Feature\Database;

use App\Models\Product;
use App\Services\BasketService;
use Database\Seeders\ProductSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasketServiceTest extends TestCase
{
    use RefreshDatabase;

    private BasketService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProductSeeder::class);
        $this->service = $this->app->make(BasketService::class);
    }

    public function test_adding_the_same_product_increments_a_single_row(): void
    {
        $this->service->add('R01');
        $this->service->add('R01');

        $this->assertDatabaseCount('basket_items', 1);
        $this->assertDatabaseHas('basket_items', [
            'product_id' => Product::where('code', 'R01')->value('id'),
            'quantity' => 2,
        ]);
    }

    public function test_the_basket_is_rebuilt_from_the_database(): void
    {
        foreach (['B01', 'B01', 'R01', 'R01', 'R01'] as $code) {
            $this->service->add($code);
        }

        $this->assertSame(9827, $this->app->make(BasketService::class)->current()->total());
    }

    public function test_removing_decrements_then_deletes(): void
    {
        $this->service->add('G01');
        $this->service->add('G01');

        $this->service->remove('G01');
        $this->assertDatabaseHas('basket_items', ['quantity' => 1]);

        $this->service->remove('G01');
        $this->assertDatabaseEmpty('basket_items');
    }

    public function test_removing_a_product_not_in_the_basket_fails(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->remove('R01');
    }

    public function test_clearing_empties_the_basket(): void
    {
        $this->service->add('R01');
        $this->service->add('G01');

        $basket = $this->service->clear();

        $this->assertDatabaseEmpty('basket_items');
        $this->assertSame(0, $basket->total());
    }

    public function test_deleting_a_product_removes_it_from_the_basket(): void
    {
        $this->service->add('R01');
        $this->service->add('G01');

        Product::where('code', 'R01')->delete();

        $this->assertDatabaseCount('basket_items', 1);
        $this->assertSame(2495 + 495, $this->service->current()->total());
    }
}
