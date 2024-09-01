<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\{Hash,Log,Storage};


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::select('id','name'
        ,'photo'
        ,'privilege'
        ,'jeton'
        )->get();
        Log::info(json_encode($users));
        return response()->json([
            'resultat' => true,
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user = new User();
        $user->name = $request->input('name');// anarana
        $user->privilege = $request->input('privilege');// privilege
        $path = $request->file('photo') ? $request->file('photo')->store(config("image.path"), 'public') : null;
        $user->photo = $path;// sary
        $user->password = Hash::make($request->input('password'));
        Log::info($user);
        return response()->json([
            'resultat' => $user->save(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        
        Log::info($request->all());
        // $user = User::find($request->input('id'))->first();
        $user = User::find($request->input('id'));
        Log::info($user);
        if($user){
            $UserData = $user;
            $user->name = $request->input('name');// anarana
            $user->privilege = $request->input('privilege');// privilege
            $path = $request->file('photo') ? $request->file('photo')->store(config("image.path"), 'public') : $UserData->photo ;
            $user->photo = $path;
            $user->password = $request->input('password') ? Hash::make($request->input('password')) : $UserData->password;// mot de passe
            return response()->json([
                'resultat' => $user->save(),
                'user'=>$user
            ]);
        }
        return response()->json([
            'resultat' => false,
            'message' => 'Utilisateur introuvable'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        //
        $user = User::find($request->all())->first();
            if($user){
                return response()->json([
                    'resultat' => $user->delete(),
                ]);
            }
            return response()->json([
                'resultat' => false,
                'message' => 'Utilisateur introuvable'
            ]);
    }
}
