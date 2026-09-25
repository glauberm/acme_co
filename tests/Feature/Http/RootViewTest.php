<?php

namespace Tests\Feature\Http;

use Tests\TestCase;

class RootViewTest extends TestCase
{
    public function test_it_serves_the_react_shell(): void
    {
        $this->withoutVite();

        $this->get('/')
            ->assertOk()
            ->assertSee('<div id="root"></div>', escape: false);
    }
}
