<?php
namespace App\Services\Voucher;
use App\Http\Resources\UserResource;
use App\Http\Resources\VoucherResource;
use App\Http\Resources\VoucherResourceCollection;
use App\Models\User;
use App\Models\Voucher;

interface IVoucher
{
    public function show(string $key, string $value): VoucherResource;

    public function generate(User $user): Voucher;

    public function getVouchersByUser(UserResource $user): VoucherResourceCollection;

    public function getVoucherBy(string $key, string $value): ?Voucher;
}
