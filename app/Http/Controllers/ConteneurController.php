<?php

namespace App\Http\Controllers;

use App\Models\Conteneur;
use Illuminate\Http\Request;
use App\Models\TypeConteneur;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class ConteneurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $conteneurs = Conteneur::all();
            if (strlen($conteneurs) > 0) {
                return response()->json([
                    'success' => true,
                    'conteneurs' => $conteneurs
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
        Log::info($request->all());
        $request->validate([
            'nom' => 'required',
            'capacite' => 'required',
            'type' => 'required'
        ]);
        // Finding the correct type from type_conteneur
        $idType = TypeConteneur::where('nom', $request->get('type'))->value('idType');
        try {
            Conteneur::create([
                'nom' => $request->get('nom'),
                'capacite' => (int)$request->get('capacite'),
                'type' => $idType
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
            $conteneur = Conteneur::find($id);
            if ($conteneur) {
                return response()->json([
                    'succes' => true,
                    'conteneur' => $conteneur
                ]);
            } else {
                return response()->json([
                    'succes' => false,
                    'message' => 'Conteneur introuvable'
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
    public function edit(Conteneur $conteneur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nom' => 'required',
            'capacite' => 'required',
            'type' => 'required',
        ]);

        $conteneur = Conteneur::find($id);

        if($conteneur) {
            $conteneur->update($data);
            return response()->json(['message' => 'Modification réussi'], 200);
        } else {
            return response()->json(['message' => 'Conteneur introuvable'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $conteneur = Conteneur::find($id);
        if($conteneur) {
           $conteneur->delete();
            return response()->json(['succes' => true, 'message' => 'Suppression réussie'], 200);
        }        
        abort(404, 'Ce conteneur n\'existe pas');
    }
}
