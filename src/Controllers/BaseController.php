<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use PDO;

abstract class BaseController
{
    protected function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    /** @return array<string, mixed> */
    protected function getJsonBody(): array
    {
        $body = \file_get_contents('php://input');
        $data = \json_decode($body ?? '', true);

        return \is_array($data) ? $data : [];
    }
}
