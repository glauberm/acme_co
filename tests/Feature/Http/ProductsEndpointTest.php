<?php

namespace Tests\Feature\Http;

use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_the_catalogue(): void
    {
        $this->seed(ProductSeeder::class);

        $this->getJson('/api/products')
            ->assertOk()
            ->assertExactJson(['data' => [
                ['code' => 'R01', 'name' => 'Red Widget', 'price' => 3295],
                ['code' => 'G01', 'name' => 'Green Widget', 'price' => 2495],
                ['code' => 'B01', 'name' => 'Blue Widget', 'price' => 795],
            ]]);
    }
}
