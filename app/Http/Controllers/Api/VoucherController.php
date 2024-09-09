<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function __construct()
    {
        //
    }

    public function generate(Request $request): JsonResponse
    {
        //
    }

    public function show(string $value, string $key = 'code'): VoucherResource
    {
        //
    }

    public function destroy(Voucher $voucher): JsonResponse
    {
        //
    }
}
