<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anlagenleistung;
use App\Models\Anlage;
use App\Models\Hersteller;
use App\Models\Leistung;
use App\Models\Typ;
use App\plugins\CubicSplines;



class PowerCalcController extends Controller
{
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
        
        //function bat_berechnung($usv_id, $leistung, $wunschautonomiezeit, $entladeschlussspanung = 1.70, $min_straenge = 1, $max_straenge = 10,$autonomiezeitmin = 0.98, $autonomiezeitmax = 1.1, $batteriehersteller = null, $batterietypid = null, $batteriebloecke = null)
        //$bat_berechnung = bat_berechnung($usv_id, ($dcleistung-($nennleistung*$cosphi*(100-$_GET["Auslastung"])/100))*1000, $_GET["Autonomiezeit"], $_GET["Entladeschlussspanung"],$_GET["Min-Batteriestraenge"],$_GET["Max-Batteriestraenge"], $_GET["Von-Zeittoleranz"]/100, $_GET["Bis-Zeittoleranz"]/100, $batteriehersteller);
    
        $wunschautonomiezeit = $request["autonomiezeit"];
        $autonomiezeitmin = $request["autonomiezeitmin"];
        $autonomiezeitmax = $request["autonomiezeitmax"];
        $entladeschlussspanung = $request["entladeschlussspanung"];
        $batteriebloecke = null;

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
        else{
            return response()->json([
                "message" => "Anlagenleistung fehlt"
            ], 404);
        }
        

        /*
        $select = "SELECT `batterietyp`.`leistungprozelle`, `batterietyp`.`id` As 'batterie_id', `batterietyp`.`name` As 'Batterietypname', `batteriehersteller`.`name` as 'Batterietyphersteller', 
									`batterietyp`.`spannung`, `batterietyp`.`zellen`, `batterietyp`.`c10` FROM `batterietyp`,`batteriehersteller` WHERE `batterietyp`.`id_hersteller` = `batteriehersteller`.`id`";
	
        $conditions = [];
        $parameters = [];

        if (!is_null($batteriehersteller)){
            $conditions[] = "batterietyp.id_hersteller = ?";
            $parameters[] = $batteriehersteller;
        }
        if(!is_null($batterietypid)){
            $conditions[] = "batterietyp.id = ?";
            $parameters[] = $batterietypid;						
        }

        if ($conditions)
        {
            $select .= " AND " . implode(" AND ", $conditions);
        }

        $query = $GLOBALS["dbh"]->prepare($select);
        $query->execute($parameters);

        while ($row = $query->fetch()){			
            if(batterie_hat_werte($row["batterie_id"])){
                if(!is_null($usv_id)){
                    $usv_bat_config = usv_bat_config($usv_id,$row["batterie_id"],$min_straenge,$max_straenge);
                }else{
                    $usv_bat_config['strang_anzahl'][0] = $max_straenge;
                    $usv_bat_config['batterie_anzahl'][0] = $batteriebloecke;
                }
                foreach ($usv_bat_config['strang_anzahl'] as $strang_anzahl) {
                    foreach ($usv_bat_config['batterie_anzahl'] as $batterie_anzahl) {
                        if ($batterie_anzahl == $batteriebloecke || is_null($batteriebloecke)){
                            $temp_zeit = zeit_berechnen($leistung, $strang_anzahl, $batterie_anzahl, $row["batterie_id"], $entladeschlussspanung);
                                if(($temp_zeit['zeit'] >= ($wunschautonomiezeit*$autonomiezeitmin) && $temp_zeit['zeit'] <= ($wunschautonomiezeit*$autonomiezeitmax) && $temp_zeit['zeit'] > 0) ||
                                ($autonomiezeitmin == null && $autonomiezeitmax == null && $temp_zeit['zeit'] > 0) ){
                                    $return_array[] =  array(
                                                'usv' => array(
                                                    'usv_id' =>	$usv_id,
                                                    'leistung' => $leistung
                                                ),
                                                'bat_zeit' => $temp_zeit['zeit'],
                                                'bat_leistung' => $temp_zeit['batterieblockleistung'],
                                                'bat_config' => array(
                                                    'batterie_id' => $row["batterie_id"],
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
        
        return $return_array;

        */
    }
}
