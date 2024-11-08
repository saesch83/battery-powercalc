<?php

namespace App\Http\Controllers;

use App\Models\Schrank;
use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use App\Models\Anlagenleistung;
use App\Models\Anlage;
use App\Models\Hersteller;
use App\Models\Leistung;
use App\Models\Typ;
use App\plugins\CubicSplines;



class PowerCalcController extends Controller
{
    
    function edit(Request $request){

        $formData["anlagen"] = Anlagenleistung::all();

        $formData["schraenke"] = Schrank::orderBy("name")->get();

        $formData["hersteller"] = Hersteller::orderBy("name")->get();
        
        for($i = 5; $i <= 100 ; $i+=5){
            $formData["load"][] = $i;
        }

        for($i = 2; $i <= 600 ; $i++){
            $formData["time"][] = $i;
        }

        for($i = 1; $i <= 99 ; $i++){
            $formData["tolerance"][] = $i;
        }

        for($i = 1.6; $i <= 1.81 ; $i+=0.01){
            $formData["cellvoltage"][] = $i;
        }

        for($i = 1; $i <= 10 ; $i++){
            $formData["strings"][] = $i;
        }

        if($request)
        {   
            $return_array = $this->batteryCalc($request);
            return view("pages.batterycalc", ["formData" => $formData, "return_array" => $return_array, "request" => $request]);
        }
        else
        {
            return view("pages.batterycalc", ["formData" => $formData]);
        }         
    }
    
    function interpolieren($inputkoordinaten, $wert)
    {
        $temp_array = [];

        foreach($inputkoordinaten as $x => $y){
            $temp_array[$x*100] = $y*100;
        }
        
        $oCurve = new CubicSplines();
        
        if ($oCurve) {	
            $oCurve->setInitCoords($temp_array, 1);
            $return_y = $oCurve->funcInterp($wert*100);
        }
        return round($return_y/100,2);
    }

    function zeit_berechnen($leistung, $straenge, $batterien, $typ, $entladeschlussspannung)
    {
        $return_array['batterieblockleistung'] = round($leistung / ($batterien*$straenge),2);
        
        $return_array['batterie_id'] = $typ["id"];
        $formel_blanko = "`BatteryBlockPower=(UPS Power)/(NumberOfBlocks*Strings)";
        $formel = $formel_blanko."=(".$leistung."W)/(".$batterien." * ".$straenge.") = ".$return_array['batterieblockleistung']."W";
        $formel_blanko .= "`";
        $temp_spg = [];
        
        if (!array_key_exists('formel', $return_array)){
            $return_array['formel'] = "";
        }
        
        for($i=0;$i<strlen($formel);$i++){ 
            $return_array['formel'] .= substr($formel,$i,1)." ";
        }
        
        //lade spannungen        
        $leistungen = Leistung::Select("spannung")->where("batterietyp_id", $typ["id"])->groupBy("spannung")->orderBy("spannung")->get();
        foreach($leistungen as $leistung){
            $temp_spg[] = $leistung["spannung"];
        }   
        
        if ( count($temp_spg) > 0 && ($entladeschlussspannung >= min($temp_spg) && $entladeschlussspannung <= max($temp_spg))){  
            
            $batt_zellen = $typ["zellen"];
            $batt_leistungprozelle = $typ["leistungprozelle"];					
            
            //interpoliere zeit aus messkurve
            $leistungen = Leistung::where("batterietyp_id", $typ["id"])->where("spannung", $entladeschlussspannung)->get();            
            foreach($leistungen as $leistung){
                if ($batt_leistungprozelle == 1){
                    $koordinaten[$leistung["leistung"]*$batt_zellen] = $leistung["zeit"];
                }else{
                    $koordinaten[$leistung["leistung"]] = $leistung["zeit"];
                }
            }
            
            if (count($leistungen) > 0){
                $return_array['zeit'] = $this->interpolieren($koordinaten,$return_array['batterieblockleistung']);
            }else{
                foreach($temp_spg as $spg){
                    $koordinaten[$spg] = $this->zeit_berechnen($leistung, $straenge, $batterien, $typ, $spg);
                }
                $return_array['zeit'] = $this->interpolieren($koordinaten,$entladeschlussspannung);
            }
                $return_array['formel'] .= " &#8793; ".$return_array['zeit']."M i n u t e s`";

        }else{
            $return_array['zeit'] = null;
        }
        
        return $return_array;
        
    }
    
    function getBatConfig($anlage, $typ, $min_straenge, $max_straenge ){
        
        $minZellen = $anlage["minZellen"];
        $maxZellen = $anlage["maxZellen"];
        $batt_zellen = $typ["zellen"];

        
        switch ($anlage["id"]) {

            //- nur gerade Anzahl Batterien
            case 6: //Galaxy 300
            case 11: //EasyUPS 3S
            case 12: //EasyUPS 3M
                     for ($i = $min_straenge; $i <= $max_straenge; $i++) {
                    $return_array['strang_anzahl'][] = $i;
                }
                for ($i = ceil($minZellen/$batt_zellen); $i <= floor($maxZellen/$batt_zellen); $i++) {
                    if ($i % 2 == 0){
                        $return_array['batterie_anzahl'][] = $i;
                    }
                }
            break;
            default: //Galaxy 9000
                for ($i = $min_straenge; $i <= $max_straenge; $i++) {
                    $return_array['strang_anzahl'][] = $i;
                }
                for ($i = ceil($minZellen/$batt_zellen); $i <= floor($maxZellen/$batt_zellen); $i++) {
                    $return_array['batterie_anzahl'][] = $i;
                }
            break;
            }

            return $return_array;
    }
    
    public function batteryCalc(Request $request){
        
        
        $wunschautonomiezeit = $request["autonomiezeit"];
        $autonomiezeitmin = $request["autonomiezeitmin"];
        $autonomiezeitmax = $request["autonomiezeitmax"];
        $entladeschlussspanung = $request["entladeschlussspanung"];
        $batteriebloecke = null;
        $return_array = [];
        $anlagenleistung = Anlagenleistung::find($request["usv_leistungs_id"]);

        if(!empty($anlagenleistung)){
            $anlage = Anlage::find($anlagenleistung->id_usv);
            
            $leistung = (($anlagenleistung->dcleistung)-(($anlagenleistung->nennleistung)*($anlage->cosphi)*(100-$request["auslastung"])/100))*1000;
            
            if($request['hersteller_id']){
                $typen = Hersteller::find($request['hersteller_id'])->typen;
            }
            else {
                $typen = Typ::all();
            }
            
            if( !empty($typen)){
                foreach($typen as $typ){
                    if(count(Leistung::Select("batterietyp_id")->where("batterietyp_id", $typ["id"])->groupBy("batterietyp_id")->get())){
                        $usv_bat_config = $this->getBatConfig($anlage, $typ, $request["min_straenge"], $request["max_straenge"] );
                        foreach ($usv_bat_config['strang_anzahl'] as $strang_anzahl) {
                            foreach ($usv_bat_config['batterie_anzahl'] as $batterie_anzahl) {
                                if ($batterie_anzahl == $batteriebloecke || is_null($batteriebloecke)){
                                    $temp_zeit = $this->zeit_berechnen($leistung, $strang_anzahl, $batterie_anzahl, $typ, $entladeschlussspanung);
                                    
                                    if(($temp_zeit['zeit'] >= ($wunschautonomiezeit*$autonomiezeitmin) && $temp_zeit['zeit'] <= ($wunschautonomiezeit*$autonomiezeitmax) && $temp_zeit['zeit'] > 0) ||
                                    ($autonomiezeitmin == null && $autonomiezeitmax == null && $temp_zeit['zeit'] > 0) ){
                                        $return_array[] =  array(
                                                    'usv' => array(
                                                        'usv_id' =>	$anlage["id"],
                                                        'usv_name' => $anlage['name'],
                                                        'leistung' => $leistung
                                                    ),
                                                    'bat_zeit' => $temp_zeit['zeit'],
                                                    'bat_leistung' => $temp_zeit['batterieblockleistung'],
                                                    'bat_config' => array(
                                                        'batterie_id' => $typ["id"],
                                                        'batterie_name' => $typ["name"],
                                                        'gewicht_total' => $typ['gewicht']*$strang_anzahl*$batterie_anzahl,
                                                        'strang_anzahl' => $strang_anzahl,
                                                        'batterie_anzahl' => $batterie_anzahl									
                                                    )
                                                );	
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            return $return_array;
        }
      
    }
}
