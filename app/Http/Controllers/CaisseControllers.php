<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaisseControllers extends Controller
{

    public function upload(Request $request){
        $uploadedFiles=$request->pics;
        foreach ($uploadedFiles as $file){
            $upload_path = public_path('upload');
            $name=$file->getClientOriginalName();
            $file->move($upload_path, $name);
            DB::table('justif_entre')
                ->insert(array(
                    'code_entre'=>$request->code
                ,'justif'=>$name));
        }
        return response('success',200);

    }

    public function upload_sortie(Request $request){
        $uploadedFiles=$request->pics;
        foreach ($uploadedFiles as $file){
            $upload_path = public_path('upload');
            $name=$file->getClientOriginalName();
            $file->move($upload_path, $name);
            DB::table('justif_sortie')
                ->insert(array(
                    'code_sortie'=>$request->code
                ,'justif'=>$name));
        }
        return response('success',200);

    }

    public function listes_justif($code){
        $data = DB::table('justif_entre')->where('code_entre','=',$code)->get();
        return response()->json($data, 201);
    }

    public function listes_justif_sortie($code){
        $data = DB::table('justif_sortie')->where('code_sortie','=',$code)->get();
        return response()->json($data, 201);
    }

    public function codeincrementer($valeur){
        $element = substr($valeur,-6);
        return ++$element;
    }

    public function generercodeentre(){
        $dernier = DB::table('entre_caisse')
            ->orderByDesc('id_entre_caisse')->first();
        if (isset($dernier)){
            $data = $this->codeincrementer($dernier->code_entre);
            $code = "OBF-ET".date('Y').$data;
        }else{
            $code = "OBF-ET".date('Y')."N00001";
        }

        $code = $this->codege($code);

        return response()->json($code, 201);


    }

    public function codege($code){
        $test = DB::table('entre_caisse')->where('code_entre','=',$code)
            ->first();
        if ($test){
            $data = $this->codeincrementer($test->code_entre);
            $code = "OBF-ET".date('Y').$data;
        }
        return $code;

    }

    public function index(){
        $valeur_entre = DB::table('entre_caisse')->sum('entre_caisse.montant_entre_caisse');
        $entree = number_format($valeur_entre,'0',',',' ');
        $valeur_sortie = DB::table('sortie_caisse')->sum('montant_sortie_caisse');
        $sortie = number_format($valeur_sortie,'0',',',' ');
        $data = array('entree'=>$entree,'sortie'=>$sortie);
        return response()->json($data, 201);
    }

    public function sortir_caisse(Request $request){
        $libelle = $request->input('libelle');
        $date_sortie = $request->input('date_sortie');
        $observation = $request->input('observation');
        $montant =(float)$request->input('montant');

        $data = array(

            'libelle_sortie_caisse'=>$libelle,
            'montant_sortie_caisse'=>$montant,
            'date_sortie_caisse'=>$date_sortie,
            'observation'=>$observation,

        );

        $clients = DB::table('sortie_caisse')->insert($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }
    }

    public function entrer_caisse(Request $request){


        $code = $request->input('code');
        $libelle = $request->input('libelle');
        $date_entre = $request->input('date_entre');
        $observation = $request->input('observation');
        $montant =(float)$request->input('montant');

        $data = array(

            'code_entre'=>$code,
            'libelle_entre_caisse'=>$libelle,
            'montant_entre_caisse'=>$montant,
            'date_entre_caisse'=>$date_entre,
            'observation'=>$observation,

        );

        $clients = DB::table('entre_caisse')->insert($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }


    public function sortirCaisse(Request $request){


        $code = $request->input('code');
        $libelle = $request->input('libelle');
        $date_entre = $request->input('date_entre');
        $observation = $request->input('observation');
        $montant =(float)$request->input('montant');

        $data = array(

            'code_sortie'=>$code,
            'libelle_sortie_caisse'=>$libelle,
            'montant_sortie_caisse'=>$montant,
            'date_sortie_caisse'=>$date_entre,
            'observation'=>$observation,

        );

        $clients = DB::table('sortie_caisse')->insert($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }

    public function paiement(Request $request){
        $personne = $request->input('personne');
        $code_facture = $request->input('code_facture');
        $date_paiement =$request->input('date_paiement');
        $montant_paiement =(float)$request->input('montant_paiement');

        $data_paiement = array(
            'id_personne'=>$personne,
            'date_montant_paiement'=>$date_paiement,
            'code_facture_entre'=>$code_facture,
            'montant_paiement'=>$montant_paiement
        );

        $paiment = DB::table('montant_paiement')->insert($data_paiement);

        if ($paiment){
            return response()->json(null, 201);
        }

        else{
            return response()->json( null,400);

        }
    }

    public function entre_listes(){
        $listes_paiement = DB::table('entre_caisse')
            ->leftJoin("justif_entre",'entre_caisse.code_entre','=','justif_entre.code_entre')
            ->select('entre_caisse.*'
                ,DB::raw("DATE_FORMAT(entre_caisse.date_entre_caisse, '%d/%m/%Y') as date_entre")
                ,DB::raw("(GROUP_CONCAT(justif_entre.justif)) as `justif`")
            )
            ->orderByDesc('entre_caisse.code_entre')
            ->groupBy("entre_caisse.code_entre")
            ->get();
        $total = DB::table('entre_caisse')->sum('montant_entre_caisse');
        $data = array("listes"=>$listes_paiement,"total"=>$total);
        return response($data,200);
    }
    public function sortie_listes(){
        $listes_paiement = DB::table('sortie_caisse')
            ->leftJoin("justif_sortie",'sortie_caisse.code_sortie','=','justif_sortie.code_sortie')
            ->select('sortie_caisse.*'
                ,DB::raw("DATE_FORMAT(sortie_caisse.date_sortie_caisse, '%d/%m/%Y') as date_sortie")
                ,DB::raw("(GROUP_CONCAT(justif_sortie.justif)) as `justif`")
            )
            ->orderByDesc('sortie_caisse.date_sortie_caisse')
            ->groupBy("sortie_caisse.code_sortie")
            ->get();
        $total = DB::table('sortie_caisse')->sum('montant_sortie_caisse');
        $data = array("listes"=>$listes_paiement,"total"=>$total);
        return response($data,200);
    }


    public function listes_factures_entree($id){

        $listes_paiement = DB::table('montant_paiement')
            ->select('montant_paiement.*','facture_entre.*'
                ,DB::raw("SUM(montant_paiement.montant_paiement) as total_payer,(facture_entre.montant_facture_entre - SUM(montant_paiement.montant_paiement)) as reste_payer"))
            ->join('facture_entre','facture_entre.code_facture_entre','=','montant_paiement.code_facture_entre')
            ->where('montant_paiement.id_personne','=',$id)
            ->get()->toJson();

        return response($listes_paiement,200);
    }


    public function listes_sorties($id){

        $listes_paiement = DB::table('sortie_caisse')
            ->where('sortie_caisse.id_personne','=',$id)
            ->get();

        $info = DB::table('personne')
            ->where('id_personne','=',$id)
            ->first();
        $marge = array('listes_paiement'=>$listes_paiement,'info'=>$info);
        return response()->json($marge,200);
    }
    public function dowload($id){
        $upload_path = public_path('upload');
        $element = DB::table('justif_entre')->where('id_justif','=',$id)->first();
        $ele = $upload_path.'/'.$element->justif;
        return response()->download($ele,$element->justif);
    }

    public function dowload_sortie($id){
        $upload_path = public_path('upload');
        $element = DB::table('justif_sortie')->where('id_justif','=',$id)->first();
        $ele = $upload_path.'/'.$element->justif;
        return response()->download($ele,$element->justif);
    }

    public function listes_versements($id){

        $listes_paiement = DB::table('montant_paiement')
            ->where('montant_paiement.code_facture_entre','=',$id)
            ->get();
        $info = DB::table('facture_entre')
            ->where('facture_entre.code_facture_entre','=',$id)
            ->first();
        $marge = array('listes_paiement'=>$listes_paiement,'info'=>$info);
        return response()->json($marge,200);
    }






    public function generercodeentre_sortie(){
        $dernier = DB::table('sortie_caisse')
            ->orderByDesc('id_sortie_sortie')->first();
        if (isset($dernier)){
            $data = $this->codeincrementer($dernier->code_sortie);
            $code = "OBF-ST".date('Y').$data;
        }else{
            $code = "OBF-ST".date('Y')."N00001";
        }

        $code = $this->codege_sortie($code);

        return response()->json($code, 201);


    }

    public function codege_sortie($code){
        $test = DB::table('sortie_caisse')->where('code_sortie','=',$code)
            ->first();
        if ($test){
            $data = $this->codeincrementer($test->code_sortie);
            $code = "OBF-ST".date('Y').$data;
        }
        return $code;

    }
}
