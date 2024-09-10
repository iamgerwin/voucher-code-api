<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\VoucherResource;
use App\Http\Resources\VoucherResourceCollection;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class VoucherController extends Controller
{
    public function __construct()
    {
        // TODO: make voucher interface and service
    }

    /**
     * Generates a new voucher for the authenticated user.
     *
     * @param Request $request The incoming HTTP request.
     * @throws \Symfony\Component\HttpFoundation\Response The HTTP response with a 422 status code if the user has already maxed out their vouchers.
     * @return JsonResponse The HTTP response with a JSON payload containing the new voucher.
     */
    public function generate(Request $request): JsonResponse
    {
        // max vouchers
        if (auth()->user()->vouchers()->count() >= 10) {
            abort(response()->json(['error' => 'Voucher already maxed out.', Response::HTTP_UNPROCESSABLE_ENTITY]));
        }

        // generate and insert voucher
        $voucher =  Voucher::create([
            'code' => $this->generateCode(),
            'user_id' => auth()->user()->id,
        ]);

        // response
        return response()->json([
           'message' => 'New voucher generated!',
           'voucher' => $voucher
        ]);
    }

    /**
     * Retrieves a collection of vouchers for the authenticated user.
     *
     * @param UserResource $userResource The resource representing the authenticated user.
     * @return VoucherResourceCollection A collection of vouchers belonging to the authenticated user.
     */
    public function index(UserResource $userResource): VoucherResourceCollection
    {
        $vouchers = auth()->user()->vouchers;
        return (new VoucherResourceCollection($vouchers));
    }


    /**
     * Retrieves a voucher by a specified key.
     *
     * @param string $key The key to retrieve the voucher by. Defaults to 'code'.
     * @param string $value The value of the key to retrieve the voucher by. Defaults to null.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the key is invalid.
     * @return VoucherResource
     */
    public function show(string $key = 'code', string $value = null): VoucherResource
    {
        $validKeys = ['id', 'code'];
        if (!in_array($key, $validKeys)) {
            abort(response()->json(['error' => 'Invalid key', Response::HTTP_BAD_REQUEST]));
        }

        $voucher = Voucher::where($key, $value)->firstOrFail();

        return new VoucherResource($voucher);
    }

    /**
     * Deletes a voucher resource.
     *
     * @param VoucherResource $voucher The voucher resource to be deleted.
     * @return JsonResponse The JSON response indicating the success of the deletion.
     */
    public function destroy(VoucherResource $voucher): JsonResponse
    {
        $voucher->delete();

        return response()->json([
            'message' => 'Successfully deleted',
        ]);
    }

    /**
     * Deletes a voucher resource by a specified key.
     *
     * @param string $key The key to delete the voucher by. Defaults to 'code'.
     * @param string $value The value of the key to delete the voucher by. Defaults to null.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the voucher does not exist or the user does not have permission to delete it.
     * @return JsonResponse The JSON response indicating the success of the deletion.
     */
    public function destroyByKey(string $key = 'code', string $value = null): JsonResponse
    {
        $voucher = Voucher::where($key, $value)->first();
        if (!$voucher && ($voucher->user_id != auth()->user()->id)) {
            abort(response()->json(['message' => 'Voucher does not exist.', Response::HTTP_FORBIDDEN]));
        }

        return $this->destroy($voucher);
    }

    /**
     * Generates a unique voucher code.
     *
     * @return string The generated voucher code.
     */
    private function generateCode(): string
    {
        do {
            $code = Str::random(5);
        } while($this->validateCode($code));

        return $code;
    }

    /**
     * Validates if a given code exists in the Voucher table.
     *
     * @param string $code The code to validate.
     * @return bool Returns true if the code exists, false otherwise.
     */
    private function validateCode(string $code): bool
    {
        return Voucher::where('code', $code)->exists();
    }
}
