<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $request;
    protected $repo;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, AuthRepository $repo)
    {
        $this->request = $request;
        $this->repo = $repo;
    }

    /**
     * Used to authenticate user
     * @post ("/api/auth/login")
     * @param LoginRequest $request
     * @Parameter("email", type="email", required="true", description="Email of User"),
     * @Parameter("password", type="password", required="true", description="Password of User"),
     * @return JsonResponse
     * })
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $auth = $this->repo->auth($request->all());
        return $this->success([
            'message' => trans('auth.logged_in'),
            'user' => $auth['user'],
            'accessToken' => $auth['accessToken'],
        ]);
    }

    /**
     * Used to check user authenticated or not
     * @post ("/api/auth/check")
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        return $this->success($this->repo->check());
    }

    /**
     * Used to logout user
     * @post ("/api/auth/logout")
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user();

        \Auth::guard('web')->logout();

        return $this->success(['message' => trans('auth.logged_out')]);
    }
}
