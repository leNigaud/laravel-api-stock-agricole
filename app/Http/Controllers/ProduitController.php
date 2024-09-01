<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Produit;
use App\Models\Stocker;
use Carbon\Traits\Date;
use App\Models\Categorie;
use App\Models\Conteneur;
use App\Models\Provenance;
use Illuminate\Http\Request;
use App\Models\TypeConteneur;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ProduitController extends Controller
{

    /**
     * Get Image of product
     */

     public function getImage($photo) // Route parameter for image path
     {
        Log::info($photo);
        $path = 'pic/' . $photo;
         if (Storage::disk('public')->exists($path)) {
            $imageContent = Storage::disk('public')->get($path);
            $contentType = Storage::mimeType($path); 
            return response($imageContent, 200)
                ->header('Content-Type', $contentType);
        } else {
             return response()->json(['message' => 'Image not found'], 404);
        }
     }

    /*

         * Display a listing of the resource.
     */
       
    public function index()
    {
        // produits
        $columns = [
            'idPro',
            'libelle',
            'photo',
            'unite',
            'vie',
            'qte',
            'idCat',
            'idTypeCont',
            'created_at',
        ];
        
        // Assuming you have a 'Produit' model
        $ps = Produit::all();
        
        $provenances = Provenance::all();
        
        // categories
        $categories = Categorie::all();

            
        $stockers = Stocker::all();
        $stock_list = array();
        // $group_stock = Stocker::groupBy('idP')->get();
        // loop through all produits in stokers
        foreach($stockers as $stock) {
            $nom_produit = $stock->produit->libelle;
            $daysToAdd = intval($stock->produit->vie);
            $date_arrivee = new DateTime($stock->date);
            $date_per = new DateTime($stock->date);
            $date_per->modify("+{$daysToAdd} days");
            $qte = $stock->quantite;
            $vie = $stock->vie;
            $provenance = $stock->provenance->LieuP;
            $conteneur = $stock->conteneur->nom;
            $st = [
                'date_per' => $date_per->format('Y-m-d'),
                'date_arrivee' => $date_arrivee->format('Y-m-d'),
                'unite' => $qte,
                'avarie_dans' => $vie,
                'qte' => intval($stock->produit->unite)*$qte,
                'conteneur' => $conteneur,
                'provenance' => $provenance
            ];
            Log::info($st);
            $item = [
                'libelle' => $nom_produit,
                'stocks' => $st
            ];
            array_push($stock_list, $item);            
        }
            
            
            
            $produits = Produit::select($columns)->get();
            $conteneurs = TypeConteneur::all();

        //     return response()->json([
        //         'produits' => $produits,
        //         'provenances' => $provenances,
        //         'categories' => $categories,
        //         'conteneurs' => $conteneurs,
        //         'stockages' => $stock_list,
                
            
            
        // ], 200)->header('Content-Type', 'application/json');
        $mesprovenances=[] ;
        foreach($provenances as $provenance) {
            $pro = [
                "id" => $provenance->idP,
                "nom" => $provenance->LieuP
            ];
            array_push($mesprovenances, $pro);
        }
                    return response()->json([
                'produits' => $produits,
                'provenances' => $mesprovenances,
                'categories' => $categories,
                'conteneurs' => $conteneurs,
                'stockages' => $stock_list,
                
            
            
        ], 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Response $response)
    {

    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        
        Log::info($request->all());
        $validatedData = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $uploadedFile = $request->file('photo');
    
        if (!$uploadedFile) {
            return response()->json(['image' => 'Please select an image to upload.'], 404);
        }
        $path = $uploadedFile->store('pic', 'public');
        $cleanedPath = str_replace('pic/', '', $path);
        // Log::info($uploadedFile);
       
        $idCat = Categorie::where('nom', $request->input('categorie'))->value('idCat');
        
        // Find type conteneur ID
        $idTypeCont = TypeConteneur::where('nom', $request->input('conteneur'))->value('idType');
  
    // Prepare data for product creation
    

    // Create the product
    Produit::create( [
        'libelle' => $request->input('libelle'),
        'qte' => 0,
        'unite' => (int)$request->input('unite'),
        'vie' => (int)$request->input('vie'),
        'idCat' => $idCat,  
        'idTypeCont' => $idTypeCont,
        'photo' => $cleanedPath 
    ]);

    // Log the data being stored
    
    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Stored successfully'
    ], 200);
}


    /**
     * Display the specified resource.
     */
    public function show($idPro)
    {
        // Informations sur le produits
        $produit = Produit::where('idPro', $idPro)->first();
        // informations sur son stockage
        $entrees = array();
        $stocks = $produit->stockers;
        
        // Parcourir tous les entrees
        foreach($stocks as $stock) {
            // get date arrive 
            $date_arr = new DateTime($stock->date);
            $date_arr->format('Y-m-d');
            $vie = $stock->produit->vie;
            $date_per = $date_arr->modify('+' . $vie . ' day')->format('Y-m-d');
            // Calcul expirée dans 
            $current_date = date('Y-m-d');
            // Check if the current date is before $date_per
            // Create DateTime objects for $date_per and current date
            $date_per_obj = new DateTime($date_per);
            $current_date_obj = new DateTime($current_date);
            // Calculate the difference between the dates
            $interval = $current_date_obj->diff($date_per_obj);
            // Get the number of days from the interval
            $days_difference = $interval->days;
           
            $entree = [
                'date_per' => $date_per,
                'date_arrivee' => $stock->date,
                'unite' => $stock->produit->unite,
                'avarie_dans' => ($current_date < $date_per)? $days_difference : 'périmé',
                'qte' => $stock->produit->Qte,
                'conteneur' => $stock->produit->typeConteneur->nom,
                'provenance' => $stock->provenance->LieuP,                
            ];
            array_push($entrees, $entree);
        }


        $data = [
            'produit' => [
                'libelle' => $produit->libelle,
                'enStock' => ($produit->Qte) > 0,
                'qte' => $produit->Qte,
                'unite' => $produit->unite,
                'vie' => $produit->vie,
                'conteneur' => $produit->typeConteneur->nom,                
            ],
            "stocks" => $entrees,
        ];
        return response()->json([], 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produit $produit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $idPro)
    {
        Log::info($request->all());
        $produit = Produit::where('idPro', $idPro)->first();
        // Log::info($produit);
        if(!$produit) {
            return response()->json([
                'succeed' => false,
                'message' => 'Produit introuvable'
            ], 404);
        }
        else {
            $data = $request->validate([
                'libelle' => 'required',
                'photo' => 'required',
                'unite' => 'required',
                'vie' => 'required',
                'categorie' => 'required',
                'conteneur' => 'required',
            ]);
            // Log::info($data);
            $idCat = Categorie::where('nom', $request->input('categorie'))->value('idCat');
            $idTypeCont = TypeConteneur::where('nom', $request->input('conteneur'))->value('idType');
    
            // Store new image 
            $validatedData = $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            $uploadedFile = $request->file('photo');
        
            if (!$uploadedFile) {
                return response()->json(['image' => 'Please select an image to upload.'], 404);
            }
            $path = $uploadedFile->store('pic', 'public');
            $cleanedPath = str_replace('pic/', '', $path);
            try {
                $produit->update([
                    'libelle' => $request->get('libelle'),
                    'photo' => $cleanedPath,
                    'unite' => $request->get('unite'),
                    'vie' => $request->get('vie'),
                    'idCat' => $idCat,
                    'idTypeCont' => $idTypeCont,
                ]);
                return response()->json([
                    'succes' => true,
                    'message' => 'Modification réussie'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'succes' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

        }
        return response()->json([
            'succes' => false,
            'message' => 'Modification échoué'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idPro)
    {
        $produit = Produit::where('idPro', $idPro)->first();
        Log::info($produit);
        if ($produit) {
            try {
                $stockers = $produit->stockers;
                foreach($stockers as $stock) {
                    $stock->delete();
                }
                $produit->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Suppression réussie',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
                        'success' => true,
                        'message' => 'Suppression réussie',
                    ], 200);
        // return response()->json([
        //     'success' => false,
        //     'message' => 'Produit non existant'
        // ], 404);
      
    }

   
}
