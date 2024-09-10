<?php

namespace App\Services\Voucher;

use App\Models\Voucher;

class GenerateVoucherService
{
    /**
     * Inserts a new voucher into the database.
     *
     * @param array $data The data to be inserted into the voucher table.
     * @return Voucher The newly created voucher.
     */
    public function insert(array $data): Voucher
    {
        return Voucher::create($data);
    }
}
