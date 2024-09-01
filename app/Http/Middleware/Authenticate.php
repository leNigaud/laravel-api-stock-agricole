<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;


class Authenticate extends Middleware {
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string {
        // return $request->expectsJson() ? null : route('login');
        return 'http://localhost:3000/login';
    }

    public function handle($request, Closure $next, ...$guards) {

        // if ($token = $request->cookie('token')) {
        //     $request->headers->set('Authorization', 'Bearer ' . $token);
        // }

        // $this->authenticate($request, $guards);

        $user = User::find($request->input('id'));
        $jeton = $request->input('jeton');
        if(!$user || $jeton != $user->jeton)
            return response()->json([
                'resultat' => false,
                'message' => 'Utilisateur non-connecté',
            ], 401);

        return $next($request);
    }
}