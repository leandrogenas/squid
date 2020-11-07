<?php

    namespace App\Http\Controllers;

    use App\Models\animes\AnimePost;
    use App\Models\animes\AnimesOnlineVip;
    use App\Models\animes\AnimesVision;
    use App\Models\animes\PostAnimesOrion;
    use App\Models\animes\Utils\FazerPostagem;
    use App\Models\filmes\TheMovieDB;
    use App\Models\wordpress\WPress;
    use App\Utils\FuncoesUteis;
    use Corcel\Model\Post;
    use Illuminate\Http\Request;

    class AnimesController extends Controller
    {
        public function pagina_atualiza_anime()
        {
            return view("screens.animes.atualiza-anime");
        }

        public function atualizar_animes(Request $request)
        {
            $log = "";
            try {
                $wp = new WPress();
                for ($i = 0; $i < count($request->post("animesonlinevip")); $i++) {
                    $id_vip = $request->post("animesonlinevip_id")[$i];
                    $id_orion = $request->post("animesorion")[$i];
                    $nome_anime = $request->post("anime")[$i];
                    $ep_start = $request->post("ep_start")[$i];
                    $ep_end = $request->post("ep_end")[$i];
                    $postar = new AnimePost($id_vip, $id_orion, $nome_anime, $wp);
                    $postar->start((int)$ep_start, (int)$ep_end);
                    if ($postar->houve_erro) {
                        $log .= $postar->log_erro . "\n";
                    }
                }
                return json_encode(["erro" => false, "log" => $log]);
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $log .= $ex->getMessage();
                return json_encode(["erro" => true, "log" => $log]);
            }
        }

        public function procurar_animesorion(Request $request)
        {
            try {
                $link = $request->post("url_animesvip");
                $link = str_replace("/", "", explode("animesonline.vip/", $link)[1]);
                $post = Post::published()->type("post")->where("post_name", $link)->first();
                $count_episodio = Post::published()->hasMeta("episodio_anime", $post->ID)->count();
                $titulo_anime = $post->title;
                $titulo_vip = $post->title;
                $dados_orion = AnimePost::buscaAnimeOrion($titulo_anime);
                $result = ["erro" => false, "title_vip" => $titulo_vip, "id_vip" => $post->ID, "post_name_vip" => $post->post_name, "episodio_total" => $count_episodio];
                $result = array_merge($dados_orion, $result);
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $result = ["erro" => true, "msg" => $ex->getMessage()];
            }
            return json_encode($result);
        }

        public function telaPosta()
        {
            return view("screens.animes.posta-anime");
        }

        public function procuraAnimeVipEOrion(Request $request){
            try {
                $url = $request->post("url");
                $link = str_replace("/", "", explode("animesorion.co/", $url)[1]);
                $post = PostAnimesOrion::published()->type("post")->where("post_name", $link)->orWhere("guid",$url)->first();
                $count_episodio = PostAnimesOrion::published()->hasMeta("episodio_anime", $post->ID)->count();
                $resultado = AnimesOnlineVip::procurar_anime($post->meta->anime_titulo);
                $result["url"] = $url;
                $result["id_orion"] = $post->ID;
                $result["title_orion"] = $post->post_title;
                $result["count_ep"] = $count_episodio;
                $result["error"] = false;
                return json_encode(array_merge($resultado, $result));
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return json_encode(["error" => true, "msg" => $ex->getMessage()]);
            }
        }

        public function procurarAnimePost(Request $request)
        {
            try {
                $url = AnimesVision::verificar_url_correta($request->post("url"));
                $titulo_anime = AnimesVision::pegar_titulo_anime($url);
                $resultado = AnimesOnlineVip::procurar_anime($titulo_anime["normal"]);
                $themovie = TheMovieDB::search_all($titulo_anime["original"],$titulo_anime["normal"]);
                $result["url"] = $url;
                $result["error"] = false;
                $result['titulo_anime'] = $titulo_anime["normal"];
                if(isset($titulo_anime['count_episodios'])){
                    $result['count_episodios'] = $titulo_anime['count_episodios'];
                }
                return json_encode(array_merge($resultado, $result,$themovie));
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return json_encode(["error" => true, "msg" => $ex->getMessage()]);
            }
        }

        public function imagemthemovie(Request $request){
            $id = $request->post("id");
            $type = $request->post("type");
            return TheMovieDB::getimagens($id,$type);
        }

        public function publicar(Request $request)
        {
//            dump($request->all());
            set_time_limit(0);
            $log = "";
            try {
                $lista = [];
                for ($i = 0; $i < count($request->post("url")); $i++) {
                    $anime = new AnimesVision($request->post("url")[$i]);
                    $anime->post_vip_id = $request->post("vip_id")[$i];
                    $anime->titulo = $request->post("anime")[$i];
                    $anime->tipo_anime = $request->post("tipo")[$i];
                    $anime->link_imagem_themovie = $request->post("imagem_themovie")[$i];
                    $anime->data = $request->post("ano")[$i];
                    $anime->carregar((int)$request->post("ep_start")[$i], (int)$request->post("ep_end")[$i]);
                    $log .= $anime->log;
                    $lista[] = $anime;
                }
                $postar = new FazerPostagem();
                $postar->postar_animes($lista);
                $log .= $postar->log;
                return json_encode(["erro" => false, "log" => $log]);
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $log .= $ex->getMessage();
                return json_encode(["erro" => true, "log" => $log]);
            }
        }
        public function telaPostagemOrion(){
            return view("screens.animes.posta-anime-orion");
        }
        public function publicarOrion(Request $request){
            set_time_limit(0);
            $log = "";
            try {
                $lista = [];
                for ($i = 0; $i < count($request->post("id_orion")); $i++) {
                    $anime = new AnimesVision("");
                    $anime->post_vip_id = $request->post("vip_id")[$i];
                    $anime->titulo = $request->post("anime")[$i];
                    $anime->carregar_orion($request->post("id_orion")[$i],(int)$request->post("ep_start")[$i], (int)$request->post("ep_end")[$i]);
                    $log .= $anime->log;
                    $lista[] = $anime;
                }
                $postar = new FazerPostagem();
                $postar->postar_animes($lista);
                $log .= $postar->log;
                return json_encode(["erro" => false, "log" => $log]);
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $log .= $ex->getMessage();
                return json_encode(["erro" => true, "log" => $log]);
            }
        }

        public function telaVerificarPostagemOrion(){
            return view("screens.animes.verifica-post");
        }
        public function verificarPostagemOrionEVip(Request $request){
            $resultado = "";
            try {
                $start_id = $request->post("start_id");
                $end_id = $request->post("end_id");
                $type = $request->post("tipo");
                $posts = PostAnimesOrion::published()->type($type)->where("ID",">=",(int)$start_id)->where("ID","<=",(int)$end_id)->get();
                foreach ($posts as $post){
                    $title = trim(FuncoesUteis::multipleReplace(["Legendado","Dublado"],"",$post->post_title));
                    if(!Post::published()->where("post_title","like",$title."%")->exists()){
                        $resultado .= $post->guid."\n";
                    }
                }
            }catch (\Throwable $ex){
                \Log::error($ex);
                $resultado .= "Houve um erro: ".$ex->getMessage()."\n";
            }
            $result["result"] = $resultado;
            return json_encode($result);
        }
    }
