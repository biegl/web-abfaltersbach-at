<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(Auth::user());
        }

        return response()->json([
            'errors' => [
                'email' => ['auth.failed'],
            ],
        ], 401);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Explicit guard: an earlier auth:sanctum-guarded request in the same process can leave
        // Auth's default guard pointed at Sanctum's RequestGuard (which has no logout()) via
        // Illuminate\Auth\Middleware\Authenticate::authenticate()'s shouldUse() call. This route
        // always authenticates via the session-based "web" guard (see authenticate() above), so
        // pin it explicitly rather than relying on the (mutable) default.
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json(['data' => 'User logged out.'], 200);
    }
}
