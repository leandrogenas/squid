<?php

namespace App\Http\Controllers;

use App\Models\filmes\Utils\PostFilmesVIP;
use Illuminate\Http\Request;

class CopiaDadosVipController extends Controller
{
    public function iniciar(Request $request){
        $iniciar_em = $request->post("iniciar_em");
        $parar_em = $request->post("parar_em");
        $posts = PostFilmesVIP::published()->type("post")->where("ID",">=",$iniciar_em)->where("ID","<=",$parar_em)->get();
    }
}
