<?php
namespace App\Services\User;
use App\Models\User;

interface IUser
{
    /**
     * Registers a new user.
     *
     * @param array $request An array containing user registration data.
     * @return User The newly created user instance.
     */
    public function register(array $request): User;

    /**
     * Authenticates a user and returns an access token.
     *
     * @param array $request An array containing user authentication data.
     * @throws HttpResponseException If the authentication attempt is unsuccessful.
     * @return array An array containing the access token and token type.
     */
    public function login(array $request): array;
}
