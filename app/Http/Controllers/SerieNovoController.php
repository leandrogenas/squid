<?php

namespace App\Http\Controllers;

use App\Enums\Sites;
use App\Models\filmes\TheMovieDB;
use App\Models\series\BkSeries;
use App\Models\series\ConfigLinksDownload;
use App\Models\series\PublicaSerie;
use App\Models\wordpress\WordPress;
use App\Utils\FuncoesUteis;
use Illuminate\Http\Request;

class SerieNovoController extends Controller
{
    public function index()
    {
        return view("screens.season.create-season");
    }

    public function postar(Request $request)
    {
        set_time_limit(0);
        try {
            if ($request->has("link")) {
                $lista = [];
                for ($i = 0; $i < count($request->post("link")); $i++) {
                    $link = $request->post("link")[$i];
                    $id_themovie = $request->post("id_themovie")[$i];
                    $serie_name = $request->post("serie_name")[$i];
                    $themovie = new TheMovieDB($id_themovie, true);
                    $config = new ConfigLinksDownload();
                    if ($request->post("todas_temporadas")[$i]) {
                        $config->pegar_tudo = true;
                    } else {
                        $config->pegar_tudo = false;
                        for ($ti = 0; $ti < count($request->post("temporada_" . $id_themovie)); $ti++) {
                            $temporada_numero = $request->post("temporada_" . $id_themovie)[$ti];
                            $episodio_start = $request->post("episodio_start_" . $id_themovie)[$ti];
                            $episodio_end = $request->post("episodio_end_" . $id_themovie)[$ti];
                            $tipos = [];
                            if ($request->post("dublado_" . $id_themovie)[$ti]) {
                                $tipos[] = "DUBLADO";
                            }
                            if ($request->post("legendado_" . $id_themovie)[$ti]) {
                                $tipos[] = "LEGENDADO";
                            }
                            $config->pegar_temporada_e_episodios[$temporada_numero] = [
                                "tipos" => $tipos,
                                "episodio_start" => $episodio_start,
                                "episodio_end" => $episodio_end
                            ];
                        }
                    }
                    $bk = new BkSeries($link);
                    $bk->theMovieDB = $themovie;
                    $bk->configLinkDownload = $config;
                    $bk->serie_name = $serie_name;
                    $lista[] = $bk;
                }
                $wordpress = new WordPress();
                $p = new PublicaSerie();
                $sites[] = Sites::SERIESONLINE_PRO;
                $p->publicar($wordpress, $lista, $sites);
            }
            $msg["msg"] = "sucesso";
            return json_encode($msg);
        } catch (\Exception $ex) {
            \Log::error($ex);
            $msg["msg"] = $ex->getMessage();
            return json_encode($msg);
        } catch (\Throwable $ex) {
            \Log::error($ex);
            $msg["msg"] = $ex->getMessage();
            return json_encode($msg);
        }

    }

    public function pesquisar(Request $request)
    {
        set_time_limit(120);
        $link_site = $request->post("link");
        $nome = FuncoesUteis::pegar_titulo_filme($link_site);
        $link_themovie = TheMovieDB::procurar_serie($nome);
        $resultado["themovie"] = $link_themovie;
        $resultado["serie"] = $nome;
        return $resultado;
    }

    public function get_progresso()
    {
        $idthemovie = \Session::get("progresso");
        if(empty($idthemovie)){
            $idthemovie = 0;
        }else{
            \Session::put("progresso",0);
            \Session::save();
        }
        return json_encode([$idthemovie]);
    }

    public function getJsonResult(){
        $json_result = \Session::get("json");
        if(empty($json_result)){
            $json_result = "";
        }else{
            \Session::put("json","");
            \Session::save();
        }
        $data["result"] = $json_result;
        return json_encode($data);
    }

    public function juntaListaJson(Request $request){
        $lista = $request->post("lista");
        $texto  = str_replace("]===================[",",",$lista);
        $texto  = str_replace("]===================","]",$lista);
        return view("screens.json.lista-json")->with(['texto'=>$texto]);
    }
}
