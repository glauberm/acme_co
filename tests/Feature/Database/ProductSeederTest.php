<?php

namespace Tests\Feature\Database;

use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_catalogue(): void
    {
        $this->seed(ProductSeeder::class);

        $this->assertDatabaseCount('products', 3);
        $this->assertDatabaseHas('products', ['code' => 'R01', 'name' => 'Red Widget', 'price' => 3295]);
        $this->assertDatabaseHas('products', ['code' => 'G01', 'name' => 'Green Widget', 'price' => 2495]);
        $this->assertDatabaseHas('products', ['code' => 'B01', 'name' => 'Blue Widget', 'price' => 795]);
    }

    public function test_it_can_be_run_repeatedly(): void
    {
        $this->seed(ProductSeeder::class);
        $this->seed(ProductSeeder::class);

        $this->assertDatabaseCount('products', 3);
    }
}
