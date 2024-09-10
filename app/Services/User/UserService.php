<?php

namespace App\Services\User;
use App\Events\UserAdded;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserService implements IUser
{

    /**
     * Registers a new user with the provided request data.
     *
     * @param array $request An array containing the user's registration data
     * @return User The newly created User instance
     */
    public function register(array $request): User
    {
        $user = User::create($request);
        UserAdded::dispatch($user);

        return $user;
    }

    /**
     * Logs in a user with the provided credentials and returns an access token.
     *
     * @param array $request The request data containing the user's credentials.
     * @throws HttpResponseException If the credentials are invalid.
     * @return array The access token and token type.
     */
    public function login(array $request): array
    {
        if (!Auth::attempt($request->validated())) throw new HttpResponseException(response: Response::HTTP_UNAUTHORIZED, previous: 'Invalid credentials');

        $user = auth()->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return [
            'accessToken' => $token,
            'token_type' => 'Bearer'
        ];
    }
}
