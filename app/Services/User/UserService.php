<?php

namespace App\Services\User;
use App\Events\UserAdded;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserService implements IUser
{

    public function register(array $request): User
    {
        $user = User::create($request);
        UserAdded::dispatch($user);

        return $user;
    }

    public function login(array $request): array
    {
        if(!Auth::attempt($request->validated())) {
            throw new HttpResponseException(response: Response::HTTP_UNAUTHORIZED, previous: 'Unauthorized');
        }

        $user = auth()->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;
        return [
            'accessToken' => $token,
            'token_type' => 'Bearer'
        ];
    }
}
