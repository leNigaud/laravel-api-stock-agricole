<?php

use App\Models\Categorie;
use App\Models\Conteneur;
use App\Models\Provenance;
use App\Models\Destination;
use Illuminate\Http\Request;
use App\Models\TypeConteneur;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ConteneurController;
use App\Http\Controllers\ProvenanceController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\TypeConteneurController;
use App\Http\Controllers\{ProfileController,UserController, HistoriqueController,AuthController};


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Get image
Route::get('/produits/images/{produit:photo}', [ProduitController::class, 'getImage']);

// Produits
Route::post('/produits/{produit}', [ProduitController::class, 'update']);
Route::get('/produits', [ProduitController::class, 'index']);
Route::post('/produits', [ProduitController::class, 'store']);
Route::get('/produits/{produit:idPro}', [ProduitController::class, 'show']);
Route::delete('/produits/{idPro}', [ProduitController::class, 'destroy']);

//fetch all data : conteneurs, categories, provenances, typeConteneurs
Route::get('/all', function() {
    
    try{
        $categories = Categorie::all();
        $provenances = Provenance::all();
        $destinations = Destination::all();
        $conteneurs = Conteneur::all();
        $typeConteneurs = TypeConteneur::all();
        
        return response()->json([
            'categories' => $categories,
            'provenances' => $provenances,
            'destinations' => $destinations,
            'conteneurs' => $conteneurs,
            'typeConteneurs' => $typeConteneurs
        ], 200);
    } catch(Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }

});
// Conteneurs
Route::get('/conteneurs', [ConteneurController::class, 'index']);
Route::post('/conteneurs', [ConteneurController::class, 'store']);
Route::get('/conteneurs/{conteneur}', [ConteneurController::class, 'show']);
Route::put('/conteneurs/{conteneur}', [ConteneurController::class, 'update']);
Route::delete('/conteneurs/{conteneur}', [ConteneurController::class, 'destroy']);

//Categoriesca
Route::post('/categories', [CategorieController::class, 'store']);
Route::get('/categories', [CategorieController::class, 'index']);
Route::get('/categories/{categorie}', [CategorieController::class, 'show']);
Route::delete('/categories/{categorie}', [CategorieController::class, 'destroy']);
Route::put('/categories/{categorie}', [CategorieController::class, 'update']);

// Proenance 
Route::post('/provenances', [ProvenanceController::class, 'store']);
Route::get('/provenances', [ProvenanceController::class, 'index']);
Route::get('/provenances/{provenance}', [ProvenanceController::class, 'show']);
Route::delete('/provenances/{provenance}', [ProvenanceController::class, 'destroy']);
Route::put('/provenances/{provenance}', [ProvenanceController::class, 'update']);

// Destination
Route::get('/destinations', [DestinationController::class, 'index']);
Route::post('/destinations', [DestinationController::class, 'store']);
Route::get('/destinations/{destination}', [DestinationController::class, 'show']);
Route::delete('/destinations/{destination}', [DestinationController::class, 'destroy']);
Route::put('/destinations/{destination}', [DestinationController::class, 'update']);

// Type Conteneur
Route::get('/typeConteneurs', [TypeConteneurController::class, 'index']);
Route::post('/typeConteneurs', [TypeConteneurController::class, 'store']);
Route::get('/typeConteneurs/{typeConteneur}', [TypeConteneurController::class, 'show']);
Route::delete('/typeConteneurs/{typeConteneur}', [TypeConteneurController::class, 'destroy']);
Route::put('/typeConteneurs/{typeConteneur}', [TypeConteneurController::class, 'update']);

    Route::get('entree',[HistoriqueController::class,'indexIN'])->name('entree');
    Route::post('entree',[HistoriqueController::class,'in'])->name('entree');
    Route::get('sortie',[HistoriqueController::class,'indexOUT'])->name('sortie');
    Route::post('sortie',[HistoriqueController::class,'out'])->name('sortie');
    Route::resource('historiques', HistoriqueController::class);
    Route::resource('utilisateurs', UserController::class);
    Route::post('utilisateur', [UserController::class, 'update']);
    Route::post('utilisateurDelete', [UserController::class, 'delete']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/image/{filename}', function ($filename) {
        // Vérifiez si le fichier existe dans le dossier public/images/
        $filePath = public_path('image/' . $filename);
        Log::info("here");
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Image not found.'], 404);
        }
    
        // Renvoie le contenu de l'image
        return response()->file($filePath);
    });
