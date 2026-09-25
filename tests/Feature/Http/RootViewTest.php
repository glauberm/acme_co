<?php

namespace Tests\Feature\Http;

use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class RootViewTest extends TestCase
{
    #[TestWith(['/'])]
    #[TestWith(['/basket'])]
    public function test_it_serves_the_react_shell(string $uri): void
    {
        $this->withoutVite();

        $this->get($uri)
            ->assertOk()
            ->assertSee('<div id="root"></div>', escape: false);
    }

    public function test_unknown_api_routes_are_not_served_the_shell(): void
    {
        $this->getJson('/api/unknown')->assertNotFound();
    }
}
