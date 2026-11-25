<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_guests_to_login_and_login_renders(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('login'))->assertStatus(200);
    }
}
