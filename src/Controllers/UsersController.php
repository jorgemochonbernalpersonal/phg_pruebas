<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Validation\Validator;

class UsersController extends BaseController
{
    private UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function index(): array
    {
        $users = $this->repository->findAll();

        return ['users' => $users];
    }

    public function store(): array
    {
        $data = $this->getJsonBody();
        $result = Validator::validateUser($data);

        if (!$result['valid']) {
            return [
                'error'   => $result['error'],
                'message' => $result['message'],
                'code'    => $result['code'],
            ];
        }

        $user = $this->repository->create($result['name'], $result['email']);

        return ['user' => $user];
    }

    public function show(string $id): array
    {
        $id = (int) $id;
        $user = $this->repository->findById($id);

        if ($user === null) {
            return ['error' => 'Not found', 'code' => 404];
        }

        return ['user' => $user];
    }

    public function update(string $id): array
    {
        $id = (int) $id;
        $data = $this->getJsonBody();
        $result = Validator::validateUser($data);

        if (!$result['valid']) {
            return [
                'error'   => $result['error'],
                'message' => $result['message'],
                'code'    => $result['code'],
            ];
        }

        $user = $this->repository->update($id, $result['name'], $result['email']);

        if ($user === null) {
            return ['error' => 'Not found', 'code' => 404];
        }

        return ['user' => $user];
    }

    public function destroy(string $id): array
    {
        $id = (int) $id;

        if (!$this->repository->delete($id)) {
            return ['error' => 'Not found', 'code' => 404];
        }

        return ['message' => 'User deleted'];
    }
}
