<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Middleware\VoucherDestroyValidation;
use App\Http\Resources\UserResource;
use App\Http\Resources\VoucherResource;
use App\Http\Resources\VoucherResourceCollection;
use App\Models\Voucher;
use App\Services\Voucher\IVoucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\Response;

class VoucherController extends Controller
{
    protected IVoucher $voucherService;

    public function __construct(IVoucher $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(VoucherDestroyValidation::class, [
                'only' => ['destroy', 'destroy.key'],
            ])
        ];
    }

    /**
     * Generates a new voucher for the authenticated user.
     *
     * @param Request $request The incoming HTTP request.
     * @return JsonResponse The HTTP response with a JSON payload containing the new voucher.
     */
    public function generate(Request $request): JsonResponse
    {
        $voucher = $this->voucherService->generate(auth()->user());

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
        return $this->voucherService->getVouchersByUser(new UserResource(auth()->user()));
    }

    /**
     * Retrieves a voucher by a specified key.
     *
     * @param string $key The key to retrieve the voucher by. Defaults to 'code'.
     * @param string|null $value The value of the key to retrieve the voucher by. Defaults to null.
     * @return VoucherResource
     */
    public function show(string $key = 'code', string $value = null): VoucherResource
    {
        return $this->voucherService->show($key, $value);
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
     * @param string|null $value The value of the key to delete the voucher by. Defaults to null.
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

}
