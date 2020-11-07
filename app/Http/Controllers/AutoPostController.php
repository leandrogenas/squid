<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutoPostController extends Controller
{
    public function configuracao(){
        return view("screens.ia.configuracao");
    }
}
