<?php

namespace App\Services;

use App\Constant\AppConstant;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            throw new HttpException(400, 'Invalid Email or Password');
        }

        $accessToken = $user->createToken('auth_token', ['*'], now()->addMinutes(AppConstant::TOKEN_EXPIRATION))->plainTextToken;
        $refreshToken = $this->generateRefreshToken($user);

        $this->userRepository->update($user->id, [
            'refresh_token' => $refreshToken,
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
        ];
    }

    public function refresh(string $refreshToken): string
    {
        $decoded = $this->decodeToken($refreshToken);

        if (!isset($decoded['sub'])) {
            throw new HttpException(401, 'Invalid refresh token');
        }

        $user = $this->userRepository->find($decoded['sub']);

        if (!$user || $user->refresh_token !== $refreshToken) {
            throw new HttpException(401, 'Invalid refresh token');
        }

        $newAccessToken = $user->createToken('auth_token', ['*'], now()->addMinutes(AppConstant::TOKEN_EXPIRATION))->plainTextToken;

        return $newAccessToken;
    }

    private function decodeToken(string $token): array
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $exp = $payload->get('exp');
            if ($exp < now()->timestamp) {
                throw new HttpException(401, 'Refresh token expired');
            }

            return $payload->toArray();
        } catch (\Exception $e) {
            throw new HttpException(401, 'Invalid refresh token');
        }
    }

    private function generateRefreshToken(User $user): string
    {
        return JWTAuth::fromUser($user, [
            'exp' => now()->addMinutes(60 * 24 * 30)->timestamp,
        ]);
    }
}
