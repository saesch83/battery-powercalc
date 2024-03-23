<?php

namespace App\Http\Controllers;

use App\Models\Anlagenleistung;
use App\Models\Hersteller;
use Illuminate\Http\Request;
use App\Models\Anlage;
use App\Models\Typ;

class DashboardController extends Controller
{
    public function index()
    {
        $data = ["anzahlAnlagen" => Anlage::count(),
                    "anzahlLeistungen" => Anlagenleistung::count(),
                    "anzahlHersteller" => Hersteller::count(),
                    "anzahlTypen" => Typ::count()
                ];

        return view('dashboard.index',$data);
    }


}
