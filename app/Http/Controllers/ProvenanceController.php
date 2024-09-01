<?php

namespace App\Http\Controllers;

use App\Models\Provenance;
use Illuminate\Http\Request;

class ProvenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $provenances = Provenance::all();
            if ($provenances->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aucun provenance trouvé'
                ], 404);
            } 

            return response()->json([
                'success' => true,
                'provenances' => $provenances
            ], 200);

        } catch (\Exception $e) {
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $data = $request->validate([
            'LieuP' => 'required'
        ]);

        // Storing new data
        try {
            Provenance::create($data);
            return response()->json([
                'success' => false,
                'message' => 'Enregistrement réussi'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Provenance $provenance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Provenance $provenance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $idP)
    {
        $provenance = Provenance::find($idP);
        if (!$provenance) {
            return response()->json([
                'success' => false,
                'message' => 'Provenance introuvable'
            ], 404);
        } 

        $data = $request->validate([
            'LieuP' => 'required'
        ]);
            
        try {
            $provenance->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Modification réussie'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
           
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Provenance $provenance)
    {
        if(!$provenance) {
            return response()->json([
                'success' => false,
                'message' => 'Provenance introuvable'
            ], 404);
        }
            try {
                $provenance->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Provenance supprimée'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
    }
}
