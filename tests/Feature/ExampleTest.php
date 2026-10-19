<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_home_page_describes_the_api(): void
    {
        $this->getJson('/')
            ->assertOk()
            ->assertJsonPath('endpoint.method', 'POST')
            ->assertJsonPath('endpoint.url', url('/api/v1/invoices/generate-pdf'));
    }
}
