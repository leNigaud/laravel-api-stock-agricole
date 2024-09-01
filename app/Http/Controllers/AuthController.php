<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Log};
use Illuminate\Http\Response;

class AuthController extends Controller {

    // login a user method
    public function login(LoginRequest $request) {
        
       $data = $request->validated();

        $user = User::where( 'name', $data['name'])->first();

        if (!$user) {
            return response()->json([
                'resultat' => false,
                'message' => 'Utilisateur introuvable'
            ], 401);
        }
        else if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'resultat' => false,
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->jeton = $token;
        $user->save();
        return response()->json([
            'resultat' => true,
            'user' => new UserResource($user),
        ]);
    }

    // logout a user method
    public function logout(Request $request) {
        // $request->user()->currentAccessToken()->delete();
        $user = User::find($request->input("id"));
        if($user){
            $user->jeton = null;
            $user->save();
        }

        return response()->json([
            'resultat' => true
        ]);
    }

    // get the authenticated user method
    public function user(Request $request) {
        // Log::info(json_encode($request->getHost()));
        return new UserResource($request->user());
    }

    
}