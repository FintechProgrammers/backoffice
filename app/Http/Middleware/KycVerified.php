<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KycVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user->kyc_is_verified) {

            $response = [
                'success' => false,
                'message' => "Kindly complete your kyc process before proceeding."
            ];

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
