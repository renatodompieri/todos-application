<?php

namespace App\Repositories;

use Illuminate\Validation\ValidationException;

class AuthRepository
{
    protected $user;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Authenticate an user.
     *
     * @param array $params
     * @return array
     * @throws ValidationException
     */
    public function auth(array $params = array()): array
    {
        $this->validateLogin($params);
        $authUser = $this->user->findByField('email', $params['email'])->first();
        $token = $authUser->createToken('access-token');

        return [
            'user' => $authUser,
            'accessToken' => $token->plainTextToken
        ];
    }

    /**
     * Validate login credentials.
     *
     * @param array $params
     * @throws ValidationException
     */
    public function validateLogin(array $params = array()): void
    {
        $email = $params['email'] ?? null;
        $password = $params['password'] ?? null;

        if (!\Auth::attempt(request()->only('email', 'password'))) {
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }
    }

    /**
     * Validate auth token.
     *
     * @return array
     */
    public function check(): array
    {
        if (!\Auth::check()) {
            return ['authenticated' => false];
        }

        return ['authenticated' => true];

    }
}
