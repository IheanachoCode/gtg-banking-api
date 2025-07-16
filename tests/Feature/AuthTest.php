<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    public function test_login_endpoint()
    {
        $response = $this->withHeaders([
            'x-api-key' => 'test-api-key-123'
        ])->postJson('/api/v1/login', [
            'userID' => 'test_user_123',
            'password' => 'password123'
        ]);

        // Debug: Let's see what the actual response looks like
        if ($response->status() !== 200) {
            dump('Response status: ' . $response->status());
            dump('Response content: ' . $response->getContent());
        }

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'token',
                'token_type',
                'expires_in',
                'user'
            ]
        ]);
    }
} 