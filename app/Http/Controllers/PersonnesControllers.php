<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonnesControllers extends Controller
{
    //
    public function rapport(Request $request){
        $date_debut = $request->date_debut;
        $date_fin = $request->date_fin;
        $date = [$date_debut,$date_fin];


        $resultat = DB::table('rapport')
            ->leftJoin('entre_caisse','entre_caisse.code_entre','=','rapport.code_entre')
            ->leftJoin('sortie_caisse','sortie_caisse.code_sortie','=','rapport.code_sortie')
            ->whereBetween('date_journe',$date)->get();

        $valeur_entre = DB::table('entre_caisse')->whereBetween('date_entre_caisse',$date)
            ->sum('entre_caisse.montant_entre_caisse');

        $valeur_sortie = DB::table('sortie_caisse')
            ->whereBetween('date_sortie_caisse',$date)
            ->sum('montant_sortie_caisse');

        $data = array('resultat'=>$resultat,'entree'=>$valeur_entre,'sortie'=>$valeur_sortie);


        return response()->json($data, 201);

    }
    public function index(){
        $clients = DB::table('personne')
            ->select('*')
            ->where('statut','=',1)
            ->get()->toJson();
        return response($clients,200);

    }

    public function createClient(Request $request) {


        $nom = $request->input('nom');
        $prenoms = $request->input('prenoms');
        $telephone = $request->input('telephone');

        $data = array(

            'nom'=>$nom,
            'prenoms'=>$prenoms,
            'telephone'=>$telephone,

        );

        $clients = DB::table('personne')->insert($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }


    public function updateClient(Request $request,$id) {
        $nom = $request->input('nom');
        $prenoms = $request->input('prenoms');
        $telephone = $request->input('telephone');

        $data = array(

            'nom'=>$nom,
            'prenoms'=>$prenoms,
            'telephone'=>$telephone,

        );

        $clients = DB::table('personne')->where('id_personne','=',$id)
            ->update($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }

    public function deleteClient ($id) {
        $update = DB::table('personne')
            ->where('id_personne','=',$id)->update(array(
                'statut'=>2
            ));
        if ($update){
            return response()->json($update, 201);
        }
        else{
            return response()->json( null,400);

        }
    }
}
