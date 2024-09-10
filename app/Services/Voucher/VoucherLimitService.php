<?php

namespace App\Services\Voucher;

use App\Models\User;

class VoucherLimitService
{
    private const MAX_VOUCHERS = 10;

    /**
     * Checks if the given user has reached the maximum allowed vouchers.
     *
     * @param User $user The user to check the voucher limit for.
     * @return bool True if the user has reached the maximum allowed vouchers, false otherwise.
     */
    public function isLimit(User $user): bool
    {
        return $user->vouchers()->count() >= self::MAX_VOUCHERS;
    }
}
