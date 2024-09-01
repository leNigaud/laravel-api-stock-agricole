<?php

namespace App\Http\Controllers;

use App\Models\TypeConteneur;
use Illuminate\Http\Request;

class TypeConteneurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $typeConteneurs = TypeConteneur::all();
            if (strlen($typeConteneurs) > 0) {
                return response()->json([
                    'success' => true,
                    'typeConteneurs' => $typeConteneurs
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Aucun conteneur trouvé'
                ], 200);
            }
            
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**p
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required'
        ]);
        try {
            TypeConteneur::create([
                'nom' => $request->input('nom'),                
            ]);
            return response()->json(['succes' => true, 'message' => 'Enregistrement conteneur réussi'], 201);
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['succes' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $typeConteneurs = TypeConteneur::find($id);
            if ($typeConteneurs) {
                return response()->json([
                    'succes' => true,
                    'typeConteneurs' => $typeConteneurs
                ]);
            } else {
                return response()->json([
                    'succes' => false,
                    'message' => 'typeConteneurs introuvable'
                ], 404);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'succes' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeConteneur $conteneur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $idType)
    {
        $data = $request->validate([
            'nom' => 'required',
        ]);

        $typeConteneur = TypeConteneur::find($idType);

        if($typeConteneur) {
            $typeConteneur->update($data);
            return response()->json(['message' => 'Modification réussi'], 200);
        } else {
            return response()->json(['message' => 'typeConteneur introuvable'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idType)
    {
        $typeConteneur = TypeConteneur::find($idType);
        if($typeConteneur) {
           $typeConteneur->delete();
            return response()->json(['succes' => true, 'message' => 'Suppression réussie'], 200);
        }        
        abort(404, 'Ce conteneur n\'existe pas');
    }
}
