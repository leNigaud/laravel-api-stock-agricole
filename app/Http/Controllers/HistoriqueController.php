<?php

namespace App\Http\Controllers;

use App\Models\{Destination,Categorie,Produit,Historique,Stocker, Conteneur, TypeConteneur, Provenance};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;



class HistoriqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categorie::all();
        $typeConteneurs = TypeConteneur::all();
        $destinations = Destination::all();
        $provenances = Provenance::all();
        $conteneurs = Conteneur::all();
        // $users = User::all();

        $historiques = Historique::query()->orderBy("created_at",'DESC')->get();
        return response()->json([
            'resultat' => true,
            'categories' => $categories,
            'destinations' => $destinations,
            'typeConteneurs' => $typeConteneurs,
            'provenances' => $provenances,
            'conteneurs' => $conteneurs,
            // 'users' => $users,
            'historiques' => $historiques
        ]);
    }
    
    public function indexIN()
    {
        //mbola  tokonny hisy lay capacité anlay conteneur
        $contenanceTotal = [];
        foreach (TypeConteneur::all() as $type) {
            // // Log::info((intval($type->conteneurs->sum("capacite")))."-".$type->produits->sum("qte").' = '.(intval($type->conteneurs->sum("capacite")) - $type->produits->sum("qte")));
            $SUMstock = 0;
            foreach ($type->produits as $p) {
                $SUMstock += intval($p->qte)/intval($p->unite);
            }
            //Log::info("Somme en stock : ".$SUMstock);
            $element = [
                'nom' => $type->nom ,
                'capacite' => (intval($type->conteneurs->sum("capacite")) - $SUMstock) 
            ];
            array_push($contenanceTotal,$element);
        }
        $produits = Produit::all();
        $categories = Categorie::all();
        $provenances = Provenance::all();
        $conteneurs = Conteneur::all();
        $typeConteneurs = TypeConteneur::all();
        // // Log::info(json_encode($contenanceTotal));
        return response()->json([
            'resultat' => true,
            'produits' => $produits,
            'categories' => $categories,
            'provenances' => $provenances,
            'contenance' => $contenanceTotal,
            'conteneurs' => $conteneurs,
            'typeconteneur' => $typeConteneurs
        ]);
    }

    public function indexOUT()
    {
        $contenanceTotal = [];
        foreach (TypeConteneur::all() as $type) {
            // Log::info((intval($type->conteneurs->sum("capacite")))."-".$type->produits->sum("qte").' = '.(intval($type->conteneurs->sum("capacite")) - $type->produits->sum("qte")));
            $SUMstock = 0;
            foreach ($type->produits as $p) {
                $SUMstock += ($p->qte)/$p->unite;
            }
            $element = [
                'nom' => $type->nom ,
                'capacite' => (intval($type->conteneurs->sum("capacite")) - $SUMstock) 
            ];
            array_push($contenanceTotal,$element);
        }
        $typeConteneurs = TypeConteneur::all();
        $produits = Produit::all();
        $categories = Categorie::all();
        $destinations = Destination::all();
        $conteneurs = Conteneur::all();
        // Log::info(json_encode($produits));
        return response()->json([
            'resultat' => true,
            'produits' => $produits,
            'categories' => $categories,
            'destinations' => $destinations,
            'contenance' => $contenanceTotal,
            'conteneurs' => $conteneurs,
            'typeconteneur' => $typeConteneurs
        ]);
    }
    
    public function in(Request $request)
    {
            $compteur = 1;
            $nbElement = count($request->all());
            $nbElement--;
            $objet = json_encode($request->all());
            $objet = json_decode($objet,true);
            $elementIN = [];
            $provenance = '';
            $resultat = true;
            $utilisateur = '';
            $pro = true; // bool miasa eo ambany X
            foreach ($objet as $obj) {
                if($compteur < $nbElement)array_push($elementIN,$obj);
                else if($pro) {
                    $provenance = $obj;
                    $pro = false;
                }
                else $utilisateur = $obj ? $obj : "Non connécté";
                $compteur++;
            }
            Log::info("elements in : ");
            Log::info($elementIN);
        // usort($elementIN, function($a, $b) {
        //     Log::info("a : ".$a['idType']);
        //     Log::info("b : ".$b['idType']);
        //     return strcmp($a['idType'], $b['idType']);
        // });
        $collect = new Collection($elementIN);
        $elementIN =  $collect->mapWithKeys(function ($elem) {
            return [$elem["idPro"] => intval($elem["uniteEntree"])];
        })->toArray();
        foreach (TypeConteneur::all() as $typeC) {
            $q = $typeC->produits->mapWithKeys(function ($produit) use ($elementIN) {
                $qte = array_key_exists($produit->getKey(), $elementIN) ? intval($elementIN[$produit->getKey()]) : 0;
                return [$produit->getKey() => $qte];
            })->toArray();
            $c = $typeC->conteneurs->mapWithKeys(function ($conteneur) {
                $reste = intval($conteneur->capacite) - intval($conteneur->produits()->sum('quantite'));
                return [$conteneur->getKey() => $reste];
            })->toArray();
            // // Log::info("quantité par produit : ".json_encode($q));
            // // Log::info("capacité par conteneur : ".json_encode($c));
            $cle = $typeC->conteneurs->pluck('idCont')->toArray();
            $compte = $typeC->produits->pluck('idPro')->toArray();
            $j = 0;
            for($i = 0 ; $i < sizeof($compte) ; $i++)
            {
                // break;
                $stored = 0;
                if(!(sizeof($q)>0 && sizeof($c)>0)) break;
                $test = $q[$compte[$i]] > $c[$cle[$j]];// bool ilaina any ambany any
                if($q[$compte[$i]] > $c[$cle[$j]]) {
                    $q[$compte[$i]] -= $c[$cle[$j]];
                    $stored = $c[$cle[$j]];
                    $c[$cle[$j]] = 0;
                }
                else{
                    $c[$cle[$j]] -= $q[$compte[$i]];
                    $stored = $q[$compte[$i]];
                    $q[$compte[$i]] = 0;
                }
                if($stored >0){
                    $stock = new Stocker;
                    $stock->idPro = $compte[$i];
                    $stock->idCont = $cle[$j];
                    $idProvenanvance = Provenance::where('LieuP',$provenance["provenance"]) ? Provenance::select('idP')->where('LieuP',$provenance["provenance"])->first() : '';
                    $stock->idP = intval($idProvenanvance->idP);
                    $stock->quantite = $stored;
                    $stock->vie = Produit::find($compte[$i]) ? Produit::find($compte[$i])->vie : 0;
                    $resultat = $resultat && $stock->save();
    
                    //produit
                    $prod = Produit::find($compte[$i]);
                    if($prod) {$prod->qte +=  ($stored * $prod->unite); $prod->save();}
    
                    //historique
                    $histo = new Historique;
                    $histo->type = "Entrée";
                    $histo->Produit = Produit::find($compte[$i]) ? Produit::find($compte[$i])->libelle : "Non-répértorié";
                    $histo->Categorie = Produit::find($compte[$i]) && Produit::find($compte[$i])->categories ? Produit::find($compte[$i])->categories->nom : "Non-répértorié";
                    $histo->unite = $stored;
                    $histo->quantite = Produit::find($compte[$i]) ? intval(Produit::find($compte[$i])->unite) * $stored : -1; // poids de l'unité * nombre d'unité donné précedemment
                    $histo->conteneur = Conteneur::find($cle[$j]) ? Conteneur::find($cle[$j])->nom : "Non-répértorié";
                    $histo->user = $utilisateur['user'];
                    $histo->Lieu = $provenance["provenance"];
                    $resultat = $resultat && $histo->save();
    
                }
                if($test) {
                    $i--;
                    $j++;
                }
                // // Log::info($histo);
                // // Log::info($stock);
            }
        }
        return response()->json([
            'resultat' => $resultat
        ]);
    }
    
    public function out(Request $request)
    {
        //
            Log::info($request->all());
            $compteur = 1;
            $nbElement = count($request->all());
            $objet = json_encode($request->all());
            $objet = json_decode($objet,true);
            $elementOUT = [];
            $destination = '';
            $resultat = true;
            $pro = true;
            $utilisateur = "";
            $nbElement--;
            foreach ($objet as $obj) {
                if($compteur < $nbElement)array_push($elementOUT,$obj);
                else if($pro) {
                    $destination = $obj;
                    $pro = false;
                }
                else $utilisateur = $obj ? $obj : "Non connécté";
                $compteur++;
            }
        usort($elementOUT, function($a, $b) {
            return strcmp($a['idPro'], $b['idPro']);
        });
        $collect = new Collection($elementOUT);
        $elementOUT =  $collect->mapWithKeys(function ($elem) {
            return [$elem["idPro"] => $elem["uniteEntree"]];
        })->toArray();
        // Log::info($elementOUT);
        $produits = Produit::all();
        $resultat = true;
        foreach ($produits as $produit) {
                $stocks = Stocker::where("idPro",intval($produit->idPro))->orderBy("vie","asc")->get();
                $quantite = array_key_exists($produit->getKey(), $elementOUT) ? intval($elementOUT[$produit->getKey()]) : 0 ;//quantité à faire sortir , $request->input ...
                foreach ($stocks as $stock) {
                    $sauverH = !($quantite == 0);
                    if(!($quantite == 0)) Log::info('quantité : '.$quantite);
                    $sorti = intval($stock->quantite)<=$quantite ? intval($stock->quantite) : $quantite ;

                    //produit
                    if($sauverH){
                        if($produit) {
                            $produit->qte -=  ($sorti * $produit->unite);
                            $produit->save();
                         }
    
                         //historique
                         $histo = new Historique;
                         $histo->type = "Sortie";
                         $histo->Produit = $produit->libelle;
                         $histo->Categorie = $produit->categories->nom;
                         $histo->unite = $sorti;
                         $histo->quantite = intval($produit->unite) * $sorti; 
                         $histo->conteneur = $stock->conteneur->nom;
                         $histo->user = $utilisateur['user'];// user 
                         $histo->Lieu = $destination["destination"]; 
                         $resultat = $resultat && $histo->save();
                        // Log::info(json_encode($histo));
                         if(intval($stock->quantite) <= $quantite){
                            $quantite -= $stock->quantite;
                            $stock->delete();
                        }else{
                            $stock->quantite = $stock->quantite - $quantite;
                            $quantite = 0;
                            $stock->save();
                        }
                    }
                }
        }
        return response()->json(['resultat' => $resultat]);
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Historique $historique)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Historique $historique)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Historique $historique)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Historique $historique)
    {
        //
    }
}
