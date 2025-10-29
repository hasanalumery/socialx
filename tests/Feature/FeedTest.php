<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
