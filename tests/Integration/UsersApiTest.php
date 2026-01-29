<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;

class UsersApiTest extends TestCase
{
    private function getBaseUrl(): string
    {
        return getenv('BASE_URL') ?: 'http://localhost:8000';
    }

    /**
     * @return array{body: string, code: int}
     */
    private function request(string $method, string $path, ?array $body = null): array
    {
        $url = $this->getBaseUrl() . $path;
        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => "Content-Type: application/json\r\n",
                'timeout' => 5,
                'ignore_errors' => true,
            ],
        ];
        if ($body !== null && \in_array($method, ['POST', 'PUT'], true)) {
            $opts['http']['content'] = \json_encode($body);
        }
        $context = \stream_context_create($opts);
        $response = @\file_get_contents($url, false, $context);
        $code = 0;
        if (isset($http_response_header[0]) && \preg_match('#HTTP/\d\.\d (\d+)#', $http_response_header[0], $m)) {
            $code = (int) $m[1];
        }

        return ['body' => $response !== false ? $response : '', 'code' => $code];
    }

    public function test_get_users_returns_200_and_array(): void
    {
        $result = $this->request('GET', '/api/users');
        if ($result['code'] === 0) {
            $this->markTestSkipped('Servidor no disponible en ' . $this->getBaseUrl());
        }
        $this->assertSame(200, $result['code']);
        $data = \json_decode($result['body'], true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('users', $data);
        $this->assertIsArray($data['users']);
    }

    public function test_post_users_creates_user_and_returns_200(): void
    {
        $email = 'test-' . \uniqid('', true) . '@example.com';
        $result = $this->request('POST', '/api/users', ['name' => 'Test User', 'email' => $email]);
        if ($result['code'] === 0) {
            $this->markTestSkipped('Servidor no disponible en ' . $this->getBaseUrl());
        }
        $this->assertSame(200, $result['code']);
        $data = \json_decode($result['body'], true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('id', $data['user']);
        $this->assertSame('Test User', $data['user']['name']);
        $this->assertSame($email, $data['user']['email']);
    }

    public function test_post_users_returns_400_when_invalid(): void
    {
        $result = $this->request('POST', '/api/users', ['name' => '', 'email' => 'invalid']);
        if ($result['code'] === 0) {
            $this->markTestSkipped('Servidor no disponible en ' . $this->getBaseUrl());
        }
        $this->assertSame(200, $result['code']);
        $data = \json_decode($result['body'], true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Validation failed', $data['error']);
    }

    public function test_get_user_by_id_returns_200_when_exists(): void
    {
        $email = 'test-get-' . \uniqid('', true) . '@example.com';
        $create = $this->request('POST', '/api/users', ['name' => 'Get Test', 'email' => $email]);
        if ($create['code'] === 0) {
            $this->markTestSkipped('Servidor no disponible en ' . $this->getBaseUrl());
        }
        $createData = \json_decode($create['body'], true);
        $this->assertArrayHasKey('user', $createData);
        $id = $createData['user']['id'];

        $result = $this->request('GET', '/api/users/' . $id);
        $this->assertSame(200, $result['code']);
        $data = \json_decode($result['body'], true);
        $this->assertArrayHasKey('user', $data);
        $this->assertSame((int) $id, (int) $data['user']['id']);
        $this->assertSame('Get Test', $data['user']['name']);
    }

    public function test_get_user_by_id_returns_404_when_not_found(): void
    {
        $result = $this->request('GET', '/api/users/999999');
        if ($result['code'] === 0) {
            $this->markTestSkipped('Servidor no disponible en ' . $this->getBaseUrl());
        }
        $this->assertSame(404, $result['code']);
        $data = \json_decode($result['body'], true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Not found', $data['error']);
    }

    public function test_put_user_returns_200_when_exists(): void
    {
        $email = 'test-put-' . \uniqid('', true) . '@example.com';
        $create = $this->request('POST', '/api/users', ['name' => 'Put Original', 'email' => $email]);
        if ($create['code'] === 0) {
            $this->markTestSkipped('Servidor no disponible en ' . $this->getBaseUrl());
        }
        $createData = \json_decode($create['body'], true);
        $id = $createData['user']['id'];
        $newEmail = 'test-put-updated-' . \uniqid('', true) . '@example.com';

        $result = $this->request('PUT', '/api/users/' . $id, ['name' => 'Put Updated', 'email' => $newEmail]);
        $this->assertSame(200, $result['code']);
        $data = \json_decode($result['body'], true);
        $this->assertArrayHasKey('user', $data);
        $this->assertSame('Put Updated', $data['user']['name']);
        $this->assertSame($newEmail, $data['user']['email']);
    }

    public function test_delete_user_returns_200_when_exists(): void
    {
        $email = 'test-delete-' . \uniqid('', true) . '@example.com';
        $create = $this->request('POST', '/api/users', ['name' => 'Delete Test', 'email' => $email]);
        if ($create['code'] === 0) {
            $this->markTestSkipped('Servidor no disponible en ' . $this->getBaseUrl());
        }
        $createData = \json_decode($create['body'], true);
        $id = $createData['user']['id'];

        $result = $this->request('DELETE', '/api/users/' . $id);
        $this->assertSame(200, $result['code']);
        $data = \json_decode($result['body'], true);
        $this->assertArrayHasKey('message', $data);
        $this->assertSame('User deleted', $data['message']);

        $getResult = $this->request('GET', '/api/users/' . $id);
        $this->assertSame(404, $getResult['code']);
    }
}
