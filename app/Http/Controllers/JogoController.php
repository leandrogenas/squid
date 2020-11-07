<?php

namespace App\Http\Controllers;

use App\Models\filmes\PublicaDados;
use App\Models\games\Skidrow;
use App\Models\wordpress\WordPress;
use Arcanedev\LogViewer\Entities\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JogoController extends Controller
{
    public function index(){
        return view("screens.games.create-games");
    }

    public function publicar(Request $request){
        set_time_limit(0);
        try {
            $progresso_nome = $request->post("progress");
            Session::put($progresso_nome, "Preparando para postar");
            Session::save();
            $wp = new WordPress();
            $publica_dados = new PublicaDados();
            $links_publicar = $request->post("link_site");
            $count = 0;
            $jogos = [];
            $sites = $request->post("sites");
            foreach ($links_publicar as $link) {
                $skidrow = new Skidrow($link);
                $skidrow->link_magnetico = $request->post("links_mag")[$count];
                $skidrow->nfo = $request->post("nfos")[$count];
                $skidrow->is_pt_br = $request->post("is_ptbr")[$count] == "true";
                $skidrow->no_torrent = $request->post("no_torrent")[$count] == "true";
                $skidrow->todas_dlc =$request->post("todas_dlc")[$count] == "true";
                $jogos[] = $skidrow;
                $count++;
            }
            $publica_dados->publicarJogos($wp,$progresso_nome,$jogos,$sites);
            Session::put($progresso_nome, "Publições Feitas");
            Session::save();
            $msg["msg"] = "sucesso";
            return json_encode($msg);
        } catch (\Exception $ex) {
            \Log::error($ex);
            $msg["msg"] = $ex->getMessage();
            return json_encode($msg);
        } catch (\Throwable $ex){
            \Log::error($ex);
            $msg["msg"] = $ex->getMessage();
            return json_encode($msg);
        }
    }
}
