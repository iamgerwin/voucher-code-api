<?php

namespace App\Services\Voucher;

use App\Models\Voucher;
use Illuminate\Support\Str;

class GenerateCodeService
{
    private const CODE_LENGTH = 5;
    /**
     * Generates a unique voucher code.
     *
     * @return string A unique voucher code of length defined by self::CODE_LENGTH.
     */
    public function generate(): string
    {
        do {
            $code = Str::random(self::CODE_LENGTH);
        } while ($this->doesExist($code));
        return $code;
    }

    /**
     * Checks if a voucher code already exists in the database.
     *
     * @param string $code The voucher code to check for existence.
     * @return bool True if the voucher code exists, false otherwise.
     */
    private function doesExist(string $code): bool
    {
        return Voucher::where('code', $code)->exists();
    }
}
