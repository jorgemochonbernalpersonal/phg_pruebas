<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function test_validate_user_returns_valid_with_correct_data(): void
    {
        $result = Validator::validateUser([
            'name'  => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertIsArray($result);
        $this->assertTrue($result['valid']);
        $this->assertSame('John Doe', $result['name']);
        $this->assertSame('john@example.com', $result['email']);
    }

    public function test_validate_user_trims_name_and_email(): void
    {
        $result = Validator::validateUser([
            'name'  => '  Jane  ',
            'email' => '  jane@example.com  ',
        ]);

        $this->assertTrue($result['valid']);
        $this->assertSame('Jane', $result['name']);
        $this->assertSame('jane@example.com', $result['email']);
    }

    public function test_validate_user_returns_error_when_name_empty(): void
    {
        $result = Validator::validateUser([
            'name'  => '',
            'email' => 'user@example.com',
        ]);

        $this->assertIsArray($result);
        $this->assertFalse($result['valid']);
        $this->assertSame('Validation failed', $result['error']);
        $this->assertSame(400, $result['code']);
        $this->assertArrayHasKey('message', $result);
    }

    public function test_validate_user_returns_error_when_email_empty(): void
    {
        $result = Validator::validateUser([
            'name'  => 'User',
            'email' => '',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertSame('Validation failed', $result['error']);
        $this->assertSame(400, $result['code']);
    }

    public function test_validate_user_returns_error_when_email_invalid(): void
    {
        $result = Validator::validateUser([
            'name'  => 'User',
            'email' => 'not-an-email',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertSame('Validation failed', $result['error']);
        $this->assertSame('invalid email', $result['message']);
        $this->assertSame(400, $result['code']);
    }

    public function test_validate_user_handles_missing_keys_as_empty(): void
    {
        $result = Validator::validateUser([]);

        $this->assertFalse($result['valid']);
        $this->assertSame(400, $result['code']);
    }
}
