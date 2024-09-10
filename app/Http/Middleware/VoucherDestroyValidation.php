<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VoucherDestroyValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $voucher = $request->route('voucher');
        $user = $request->user();

        if ($user->id != $voucher->user_id) {
            return response()->json([
                'message' => 'Invalid Request',
                Response::HTTP_FORBIDDEN
            ]);
        }

        return $next($request);
    }
}
