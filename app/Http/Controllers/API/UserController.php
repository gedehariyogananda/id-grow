<?php

namespace App\Http\Controllers\API;

use App\Helper\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        try {
            $users = $this->userService->get(request(User::$allowedParams));
            return ApiResponseHelper::paginated($users);
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function detail($id)
    {
        try {
            $user = $this->userService->find($id);
            if (!$user) {
                return ApiResponseHelper::error('User not found', 404);
            }
            return ApiResponseHelper::success($user, 'User details retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }
}
