<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hersteller;

class HerstellerController extends Controller
{
    public function index(Request $request){
        $hersteller = Hersteller::all();
        
        if($request->expectsJson()){
            return response()->json($hersteller);
        }else{
            return view("dashboard.hersteller", ["herstellerliste" => $hersteller]);
        }
    }

    public function show($id){
        $hersteller = Hersteller::find($id);

        if(!empty($hersteller)){
            return response()->json($hersteller);
        }
        else{
            return response()->json([
                "message" => "Hersteller nicht gefunden"
            ], 404);
        }
    }

    public function typen($id){
        $hersteller = Hersteller::find($id);

        if(!empty($hersteller)){
            return response()->json($hersteller->typen);
        }
        else{
            return response()->json([
                "message" => "Hersteller nicht gefunden"
            ], 404);
        }
    }
}
