<?php

namespace Tests\Feature\Http;

use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Tests\TestCase;
use Tests\Unit\BasketCalculatorServiceTest;

class BasketEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProductSeeder::class);
    }

    public function test_the_basket_starts_empty(): void
    {
        $this->getJson('/api/basket')
            ->assertOk()
            ->assertExactJson(['data' => [
                'items' => [],
                'subtotal' => 0,
                'discount' => 0,
                'delivery' => 0,
                'total' => 0,
            ]]);
    }

    public function test_adding_a_product_returns_the_updated_basket(): void
    {
        $this->postJson('/api/basket/items', ['code' => 'R01'])->assertOk();

        $this->postJson('/api/basket/items', ['code' => 'R01'])
            ->assertOk()
            ->assertExactJson(['data' => [
                'items' => [
                    ['code' => 'R01', 'name' => 'Red Widget', 'price' => 3295, 'quantity' => 2],
                ],
                'subtotal' => 6590,
                'discount' => 1648,
                'delivery' => 495,
                'total' => 5437,
            ]]);
    }

    #[DataProviderExternal(BasketCalculatorServiceTest::class, 'exampleBaskets')]
    public function test_example_basket_totals(array $codes, int $expectedTotal): void
    {
        foreach ($codes as $code) {
            $this->postJson('/api/basket/items', ['code' => $code])->assertOk();
        }

        $this->getJson('/api/basket')->assertJsonPath('data.total', $expectedTotal);
    }

    public function test_adding_requires_a_known_product_code(): void
    {
        $this->postJson('/api/basket/items', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');

        $this->postJson('/api/basket/items', ['code' => 'X01'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');

        $this->assertDatabaseEmpty('basket_items');
    }

    public function test_removing_a_product_removes_one_unit(): void
    {
        $this->postJson('/api/basket/items', ['code' => 'G01']);
        $this->postJson('/api/basket/items', ['code' => 'G01']);

        $this->deleteJson('/api/basket/items/G01')
            ->assertOk()
            ->assertJsonPath('data.items.0.quantity', 1)
            ->assertJsonPath('data.total', 2495 + 495);

        $this->deleteJson('/api/basket/items/G01')
            ->assertOk()
            ->assertJsonPath('data.items', []);
    }

    public function test_removing_a_product_not_in_the_basket_is_not_found(): void
    {
        $this->deleteJson('/api/basket/items/R01')->assertNotFound();
        $this->deleteJson('/api/basket/items/X01')->assertNotFound();
    }

    public function test_clearing_the_basket(): void
    {
        $this->postJson('/api/basket/items', ['code' => 'R01']);
        $this->postJson('/api/basket/items', ['code' => 'B01']);

        $this->deleteJson('/api/basket')
            ->assertOk()
            ->assertJsonPath('data.items', [])
            ->assertJsonPath('data.total', 0);

        $this->assertDatabaseEmpty('basket_items');
    }
}
