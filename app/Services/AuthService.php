<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    public function login(array $credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('Invalid Email or Password');
        }

        $tokenExpiration = isset($credentials['me']) && $credentials['me']
            ? 60 * 24 * 7
            : 60 * 4;

        $token = $user->createToken('auth_token', ['*'], now()->addMinutes($tokenExpiration))->plainTextToken;

        return [
            'token' => $token,
            'expires_in' => $tokenExpiration * 60,
            'token_type' => 'Bearer',
        ];
    }
}
