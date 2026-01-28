<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;

class HealthTest extends TestCase
{
    public function test_api_responds_ok(): void
    {
        $base = getenv('BASE_URL') ?: 'http://localhost:8000';
        $response = @file_get_contents($base . '/');
        if ($response === false) {
            $this->markTestSkipped('Servidor no disponible en ' . $base);
        }
        $data = json_decode($response, true);
        $this->assertIsArray($data);
        $this->assertSame('ok', $data['status'] ?? null);
    }
}
