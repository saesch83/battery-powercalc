<?php

namespace App\Http\Controllers;

use App\Models\Anlagenleistung;
use Illuminate\Http\Request;
use App\Models\Anlage;

class AnlagenleistungController extends Controller
{
    public function index(Request $request)
    {   
        $anlagenleistungen = Anlagenleistung::all();

        if ($request->expectsJson()) {
            return response()->json($anlagenleistungen);
        } else {
            
        }
    }

    public function groupedByAnlage(Request $request)
    {   
        $anlagenleistungen = Anlagenleistung::all();
        $result = [];
        
        foreach ($anlagenleistungen as $leistung) {
            
            if( !array_key_exists($leistung->anlage->id, $result)){
                $result[$leistung->anlage->id]["USVname"] = $leistung->anlage->name;
                $result[$leistung->anlage->id][50] = [];
                $result[$leistung->anlage->id][100] = [];
                $result[$leistung->anlage->id][200] = [];
                $result[$leistung->anlage->id][400] = [];
                $result[$leistung->anlage->id]["max"] = [];
            }

            if(intval($leistung->dcleistung) < 50 )
                $result[$leistung->anlage->id][50][$leistung->id] = $leistung->dcleistung;
            else if(intval($leistung->dcleistung) > 50 && intval($leistung->dcleistung) < 100)
                $result[$leistung->anlage->id][100][$leistung->id] = $leistung->dcleistung;
            else if(intval($leistung->dcleistung) > 100 && intval($leistung->dcleistung) < 200)
                $result[$leistung->anlage->id][200][$leistung->id] = $leistung->dcleistung;
            else if(intval($leistung->dcleistung) > 200 && intval($leistung->dcleistung) < 400)
                $result[$leistung->anlage->id][400][$leistung->id] = $leistung->dcleistung;
            else if(intval($leistung->dcleistung) > 400)
                $result[$leistung->anlage->id]["max"][$leistung->id] = $leistung->dcleistung;
        }
        
        return view('dashboard.anlagenleistungen', ["anlagenleistungen" => $result]);
        
    }

}
