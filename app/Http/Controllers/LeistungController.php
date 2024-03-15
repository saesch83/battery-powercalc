<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leistung;

class LeistungController extends Controller
{
    public function index(){
        $leistungen = Leistung::simplePaginate(1000);
        return response()->json($leistungen);
    }

    public function show($id){
        $leisung = Leistung::find($id);

        if(!empty($leisung)){
            return response()->json($leisung);
        }
        else{
            return response()->json([
                "message" => "Angegebene Leistung nicht gefunden"
            ], 404);
        }
    }
}
