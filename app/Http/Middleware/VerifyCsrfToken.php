<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
        '/api/produits',
        '/api/logout',
        '/api/login',
        '/api/entree',
        '/api/sortie',
        '/api/utilisateurs',
        '/api/utilisateur',
        '/api/utilisateurDelete',
        '/api/image',
        '/api/user',
    ];
}
