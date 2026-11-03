<?php

namespace Tests\Feature;

use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    public function test_google_auth_url_endpoint_returns_valid_oauth_url(): void
    {
        $response = $this->getJson('/api/auth/google/url');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'url']);
        $this->assertStringContainsString('accounts.google.com', $response->json('url'));
        $this->assertStringContainsString('522625435967', $response->json('url'));
    }
}

