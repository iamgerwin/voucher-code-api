<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\User\IUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(IUser $userService)
    {
        $this->userService = $userService;
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
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $user = $this->userService->register($request->validated());
        return response()->json(new UserResource($user), Response::HTTP_CREATED);
    }

    /**
     * Authenticates a user and returns a JSON response with the login data.
     *
     * @param LoginRequest $request The request object containing the login credentials.
     * @return JsonResponse A JSON response with the login data.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->userService->login($request->validated());
        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * Logs out the current user by deleting their tokens.
     *
     * @param Request $request The incoming request object.
     * @return JsonResponse A JSON response with a success message.
     */
    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'logged out!',
        ]);
    }
}
