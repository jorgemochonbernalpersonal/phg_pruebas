<?php

declare(strict_types=1);

namespace App\Validation;

class Validator
{
    /**
     * Valida name y email para usuario.
     * @return array{valid: true, name: string, email: string}|array{valid: false, error: string, message: string, code: int}
     */
    public static function validateUser(array $data): array
    {
        $name = \trim((string) ($data['name'] ?? ''));
        $email = \trim((string) ($data['email'] ?? ''));

        if ($name === '' || $email === '') {
            return [
                'valid'   => false,
                'error'   => 'Validation failed',
                'message' => 'name and email are required',
                'code'    => 400,
            ];
        }

        if (!\filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid'   => false,
                'error'   => 'Validation failed',
                'message' => 'invalid email',
                'code'    => 400,
            ];
        }

        return [
            'valid'  => true,
            'name'   => $name,
            'email'  => $email,
        ];
    }
}
