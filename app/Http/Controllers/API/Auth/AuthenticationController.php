<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticationController extends Controller
{
    public function login(LoginRequest $request)
    {
        $this->ensureNotRateLimited($request->email, $request->ip());

        RateLimiter::hit($this->throttleKey($request->email, $request->ip()));

        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new UnauthorizedHttpException(__("auth.failed"));
        }

        $token = $user->createToken($request->userAgent(), ["basic"])->plainTextToken;
        RateLimiter::clear($this->throttleKey($request->email, $request->ip()));

        return \response()->json([
            "message" => "Login success",
            "code" => 200,
            "data" => [
                "access_token"  => $token,
                "user" => $user
            ],
        ], 200);
    }
    public function ensureNotRateLimited($email, $ip)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($email, $ip), 3)) {
            return;
        }
    }

    public function throttleKey($email, $ip)
    {
        return \trans($email . "|" . $ip, [
            "seconds" => "",
            "minutes" => "",
        ]);
    }
}
