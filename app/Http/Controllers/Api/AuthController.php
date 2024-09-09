<?php

namespace App\Http\Controllers\Api;

use App\Events\UserAdded;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $userService;

    public function __construct()
    {
        //TODO: make user interface & service
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="name", type="string", example="John"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="password", type="string", example="password123"),
     *                 required={"username", "name", "email", "password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(UserRegisterRequest $request)
    {
        $user = User::create($request);

        UserAdded::dispatch($user);

        return $user;
    }

    public function login(LoginRequest $request)
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

    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'logged out!',
        ]);
    }
}
