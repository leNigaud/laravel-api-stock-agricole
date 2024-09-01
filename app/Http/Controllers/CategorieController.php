<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Categorie::all();
            if(strlen($categories) > 0) {
                return response()->json([
                    'success' => true,
                    'categories' => $categories
                ], 200);
            } else {
                return response()->json([
                    'succes' => true,
                    'message' => 'Aucun catégorie trouvé'
                ], 200);
            }            
        } catch (\Exception $e) {
            return response()->json([
                'succes' => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $data = $request->validate([
            'nom' => 'required'
        ]);
        try {
            $success = Categorie::create($data);
            if($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Enregistré avec succès'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Enregistrement échoué'
                ], 400);
            }
        } catch(Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        $categorie = Categorie::find($categorie);
        if($categorie) {
            return response()->json([
                'categorie' => $categorie
            ], 200);
        }
        abort(404, 'Categorie introuvable');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $idCat)
    {
        Log::info(json_encode($request->all()));
        // Validation des données
        $categorie = Categorie::findOrFail($idCat);
        $data = $request->validate([
            'nom' => 'required'
        ]);

        // Recherche categorie correspondante destination
        if($categorie) {
            try {
                $categorie->update($data);
                return response()->json([
                    'success' => true,
                    'message'=> 'Modification réussie'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Erreur durant la modification'
            ], 400);
        }
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idCat)
    {
        $categorie = Categorie::find($idCat);
        if($categorie) {
            try {
                $categorie->delete();
                return response()->json([
                    'success' => true,
                    'message'=> 'Suppression réussie'
                ], 200);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de suppression'
            ], 400);
        }
    }
}
