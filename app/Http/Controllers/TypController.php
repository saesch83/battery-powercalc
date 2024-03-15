<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Typ;

class TypController extends Controller
{
    public function index(){
        $typen = Typ::all();
        return response()->json($typen);
    }

    public function show($id){
        $typ = Typ::find($id);

        if(!empty($typ)){
            return response()->json($typ);
        }
        else{
            return response()->json([
                "message" => "Typ nicht gefunden"
            ], 404);
        }
    }

    public function hersteller($id){
        $typ = Typ::find($id);

        if(!empty($typ)){
            return response()->json($typ->hersteller);
        }
        else{
            return response()->json([
                "message" => "Typ nicht gefunden"
            ], 404);
        }
    }
}
