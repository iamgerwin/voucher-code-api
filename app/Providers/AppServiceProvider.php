<?php

namespace App\Providers;

use App\Services\User\IUser;
use App\Services\User\UserService;
use App\Services\Voucher\IVoucher;
use App\Services\Voucher\VoucherService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IVoucher::class, VoucherService::class);
        $this->app->bind(IUser::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
