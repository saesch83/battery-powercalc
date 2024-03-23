<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anlage;

class AnlageController extends Controller
{
    public function index(Request $request)
    {   
        $anlagen = Anlage::all();

        if ($request->expectsJson()) {
            return response()->json($anlagen);
        } else {
            return view('dashboard.anlagen', ["anlagen" => $anlagen]);
        }
    }
}
