<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = new Router();
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        parent::tearDown();
    }

    private function dispatchAndCapture(): string
    {
        \ob_start();
        $this->router->dispatch();
        return (string) \ob_get_clean();
    }

    public function test_dispatch_returns_404_for_unknown_route(): void
    {
        $this->router->get('/known', fn () => ['status' => 'ok']);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/unknown';

        $output = $this->dispatchAndCapture();
        $data = \json_decode($output, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Not Found', $data['error']);
    }

    public function test_dispatch_matches_exact_route_and_returns_handler_result(): void
    {
        $this->router->get('/health', fn () => ['status' => 'ok']);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/health';

        $output = $this->dispatchAndCapture();
        $data = \json_decode($output, true);

        $this->assertIsArray($data);
        $this->assertSame('ok', $data['status']);
    }

    public function test_dispatch_matches_route_with_id_parameter(): void
    {
        $this->router->get('/api/users/{id}', fn (string $id) => ['user_id' => $id]);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/users/42';

        $output = $this->dispatchAndCapture();
        $data = \json_decode($output, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertSame('42', $data['user_id']);
    }

    public function test_dispatch_uses_custom_code_from_handler(): void
    {
        $this->router->get('/gone', fn () => ['error' => 'Gone', 'code' => 410]);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/gone';

        $this->dispatchAndCapture();
        $this->assertSame(410, \http_response_code());
    }
}
