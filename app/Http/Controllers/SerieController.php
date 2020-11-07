<?php

namespace App\Http\Controllers;

use App\Models\filmes\Bludv;
use App\Models\filmes\Lapumia;
use App\Models\filmes\PublicaDados;
use App\Models\filmes\sites\ComandoTorrent;
use App\Models\filmes\TheMovieDB;
use App\Models\IMDB;
use App\Models\series\AtualizaPostagem;
use App\Models\wordpress\WordPress;
use App\Utils\FuncoesUteis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SerieController extends Controller
{

    public function atualizar_serie_tela(){
        return view("screens.series.update-serie");
    }

    public function atualizar_serie(Request $request){
        set_time_limit(0);
        try {
            $progresso_nome = $request->post("progress");
            Session::put($progresso_nome, "Preparando para atualizar");
            Session::save();
            $wp = new WordPress();
            $publica_dados = new PublicaDados();
            \Log::debug(print_r($request->all(),true));
            $links_publicar = $request->post("link_site");
            $count = 0;
            $series = [];
            $sites = $request->post("sites");
            foreach ($links_publicar as $link) {
                $atualiza_serie = new AtualizaPostagem();
                foreach ($sites as $site){
                    $atualiza_serie->set_post_edit($site,$request->post($site));
                }
                $filme = $this->identificar_e_preparar_site($link, $request->post("id_themovies")[$count],
                    $request->post("id_imdbs")[$count],true);
                $atualiza_serie->filme = $filme;
                $series[] = $atualiza_serie;
                $count++;
            }
            $publica_dados->atualizar_serie($wp,$progresso_nome,$series,$sites);
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

    public function pesquisar_links_serie_atualiza(Request $request)
    {
        set_time_limit(0);
        $link_site = $request->post("link");
        $nome = FuncoesUteis::pegar_titulo_filme($link_site);
        $link_themovie = TheMovieDB::procurar_serie($nome);
        $link_imdb = IMDB::procurar_serie($nome);
        $post_edits = FuncoesUteis::procurar_ID_postagem($nome);
        $resultado["themovie"] = $link_themovie;
        $resultado["imdb"] = $link_imdb;
        $resultado["post_site"] = $post_edits;
        $resultado['serie'] = $nome;
        return $resultado;
    }

    public function pesquisa_links_somente_serie(Request $request){
        set_time_limit(120);
        $link_site = $request->post("link");
        $nome = FuncoesUteis::pegar_titulo_filme($link_site);
        $link_themovie = TheMovieDB::procurar_serie($nome);
        $resultado["themovie"] = $link_themovie;
        $resultado["serie"] = $nome;
        return $resultado;
    }

    private function identificar_e_preparar_site($link_site, $id_themovie, $id_imdb,$is_serie = false)
    {
        if (Str::contains($link_site, "torrentdosfilmes")) {
            $lapumia = new Lapumia($link_site);
            $themovie = new TheMovieDB($id_themovie,$is_serie);
            $imdb = new IMDB($id_imdb);
            $lapumia->theMovieDB = $themovie;
            $lapumia->imdb = $imdb;
            $lapumia->is_serie = $is_serie;
            return $lapumia;
        }else if(Str::contains($link_site, "comandotorrentshd")){
            $bludv = new Bludv($link_site);
            $themovie = new TheMovieDB($id_themovie,$is_serie);
            $imdb = new IMDB($id_imdb);
            $bludv->theMovieDB = $themovie;
            $bludv->imdb = $imdb;
            $bludv->is_serie = $is_serie;
            return $bludv;
        }else if(Str::contains($link_site, "comandotorrents.org")){
            $comando = new ComandoTorrent($link_site);
            $themovie = new TheMovieDB($id_themovie,$is_serie);
            $imdb = new IMDB($id_imdb);
            $comando->theMovieDB = $themovie;
            $comando->imdb = $imdb;
            $comando->is_serie = $is_serie;
            return $comando;
        } else {
            return null;
        }
    }
}
