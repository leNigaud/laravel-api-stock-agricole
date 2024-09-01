<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $destinations = Destination::all();
            Log::info($destinations);
            if ($destinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun Destination trouvé'
                ], 404);
            } 

            return response()->json([
                'success' => true,
                'destinations' => $destinations
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
            'LieuD' => 'required'
        ]);

        // Storing new data
        try {
            Destination::create($data);
            return response()->json([
                'success' => true   ,
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
    public function show(Destination $destination)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $idD)
    {
        $destination = Destination::findOrFail($idD);
        $data = $request->validate([
            'LieuD' => 'required',
        ]);
        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Destination introuvable'
            ], 404);
        } 

                  
        try {
            $destination->update($data);
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
    public function destroy(Destination $destination)
    {
        if(!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Destination introuvable'
            ], 404);
        }
            try {
                $destination->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Destination supprimée'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
    }
}
