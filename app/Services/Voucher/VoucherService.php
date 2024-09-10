<?php
namespace App\Services\Voucher;
use App\Http\Resources\UserResource;
use App\Http\Resources\VoucherResource;
use App\Http\Resources\VoucherResourceCollection;
use App\Models\User;
use App\Models\Voucher;
use Symfony\Component\HttpFoundation\Response;

class VoucherService implements IVoucher
{

    /**
     * Initializes the VoucherService instance with required dependencies.
     *
     * @param GenerateCodeService $generateCodeService Service for generating voucher codes.
     * @param GenerateVoucherService $generateVoucherService Service for generating vouchers.
     * @param VoucherLimitService $voucherLimitService Service for managing voucher limits.
     * @return void
     */
    public function __construct(protected GenerateCodeService $generateCodeService,
                                protected GenerateVoucherService $generateVoucherService,
                                protected VoucherLimitService $voucherLimitService)
    {
        //
    }

    /**
     * Generates a new voucher for the given user.
     *
     * @param User $user The user for whom the voucher is being generated.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the user has reached the voucher limit.
     * @return Voucher The generated voucher.
     */
    public function generate(User $user): Voucher
    {
        if ($this->voucherLimitService->isLimit($user)) {
            abort(response()->json([
                'error' => 'Voucher already maxed out.',
                Response::HTTP_UNPROCESSABLE_ENTITY]));
        }

        return $this->generateVoucherService->insert([
           'user_id' => $user->id,
           'code' => $this->generateCodeService->generate(),
        ]);
    }

    /**
     * Retrieves a collection of vouchers for the given user.
     *
     * @param UserResource $user The user for whom the vouchers are being retrieved.
     * @return VoucherResourceCollection A collection of vouchers belonging to the user.
     */
    public function getVouchersByUser(UserResource $user): VoucherResourceCollection
    {
        return new VoucherResourceCollection($user->vouchers);
    }

    /**
     * Retrieves a voucher based on the given key and value.
     *
     * @param string $key The key to search for in the voucher.
     * @param string $value The value to search for in the voucher.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no voucher is found.
     * @return Voucher|null The voucher if found, or null if not found.
     */
    public function getVoucherBy(string $key, string $value): ?Voucher
    {
        return Voucher::where($key, $value)->firstOrFail();
    }

    /**
     * Retrieves a voucher resource based on the given key and value.
     *
     * @param string $key The key to search for in the voucher.
     * @param string $value The value to search for in the voucher.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no voucher is found.
     * @return VoucherResource The voucher resource if found.
     */
    public function show(string $key, string $value): VoucherResource
    {
        $validKeys = ['id', 'code'];
        if (!in_array($key, $validKeys)) {
            abort(response()->json(['error' => 'Invalid key', Response::HTTP_BAD_REQUEST]));
        }

        return new VoucherResource(Voucher::where($key, $value)->firstOrFail());
    }
}
