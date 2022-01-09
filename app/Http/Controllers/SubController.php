<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubController extends Controller
{
    public function checkSubs(Request $request){
        $rnd = rand(0,1);

        if($rnd == 0){
            return response()->json(['status'=>true]);
        } else {
            return response()->json(['status'=>false]);
        }
    }
}
