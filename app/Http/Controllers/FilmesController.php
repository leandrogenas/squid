<?php

    namespace App\Http\Controllers;

    use App\Enums\Sites;
    use App\Models\filmes\Bludv;
    use App\Models\filmes\Lapumia;
    use App\Models\filmes\PublicaDados;
    use App\Models\filmes\sites\ComandoTorrent;
    use App\Models\filmes\TheMovieDB;
    use App\Models\filmes\Utils\BuscaImagemOffline;
    use App\Models\Imagens;
    use App\Models\IMDB;
    use App\Models\wordpress\WordPress;
    use App\Utils\FuncoesUteis;
    use HieuLe\WordpressXmlrpcClient\WordpressClient;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Str;
    use voku\helper\HtmlDomParser;

    class FilmesController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            //
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            return view("screens.movies.create-movies");
        }

        public function create_serie()
        {
            return view("screens.series.create-series");
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @return false|string
         */
        public function store(Request $request)
        {
            set_time_limit(0);
            try {
                $progresso_nome = $request->post("progress");
                Session::put($progresso_nome, "Preparando para postar");
                Session::save();
                $wp = new WordPress();
                $publica_filme = new PublicaDados();
                $links_publicar = $request->post("link_site");
                $count = 0;
                $filmes = [];
                $sites = $request->post("sites");
                $is_serie = $request->has("serie");
                foreach ($links_publicar as $link) {
                    $filme = $this->identificar_e_preparar_site($link,$request->post("movie_name")[$count], $request->post("id_themovies")[$count],
                        $request->post("id_imdbs")[$count], $is_serie, $request->post("is_cinema")[$count] == "true");
                    $filmes[] = $filme;
                    $count++;
                }
                if ($is_serie) {
                    $publica_filme->publicarSerie($wp, $progresso_nome, $filmes, $sites);
                } else {
                    $publica_filme->publicarFilmes($wp, $progresso_nome, $filmes, $sites);
                }
                Session::put($progresso_nome, "Publições Feitas");
                Session::save();
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

        /**
         * Display the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function show($id)
        {
            //
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function edit($id)
        {
            //
        }

        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id)
        {
            //
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function destroy($id)
        {
            //
        }

        public function pesquisar_links(Request $request)
        {
            set_time_limit(120);
            $link_site = $request->post("link");
            $nome = FuncoesUteis::pegar_titulo_filme($link_site);
            $link_themovie = TheMovieDB::procurar_filme($nome);
            $link_imdb = IMDB::procurar_filme($nome);
            $resultado["themovie"] = $link_themovie;
            $resultado["imdb"] = $link_imdb;
            $resultado["movie"] = $nome;
            return $resultado;

        }

        public function pesquisar_links_serie(Request $request)
        {
            set_time_limit(120);
            $link_site = $request->post("link");
            $nome = FuncoesUteis::pegar_titulo_filme($link_site);
            $link_themovie = TheMovieDB::procurar_serie($nome);
            $link_imdb = IMDB::procurar_serie($nome);
            $resultado["themovie"] = $link_themovie;
            $resultado["imdb"] = $link_imdb;
            $resultado["serie"] = $nome;
            return $resultado;
        }

        public function get_progresso(Request $request)
        {
            $progresso = $request->get("p");
            $filmePostado = \Session::get("$progresso-filme");
            $site_publicando = Session::get("$progresso-site");
            if (empty($filmePostado)) {
                $filmePostado = 0;
            }
            if (empty($site_publicando)) {
                $site_publicando = 0;
            }
            return json_encode([\Session::get($progresso), $filmePostado, $site_publicando]);

        }

        private function identificar_e_preparar_site($link_site, $movie_name,$id_themovie, $id_imdb, $is_serie = false, $is_cinema = false)
        {

            if (\Str::contains($link_site, "torrentdosfilmes")) {
                $lapumia = new Lapumia($link_site);
                $themovie = new TheMovieDB($id_themovie, $is_serie);
                $imdb = new IMDB($id_imdb);
                $lapumia->theMovieDB = $themovie;
                $lapumia->imdb = $imdb;
                $lapumia->is_serie = $is_serie;
                $lapumia->is_cinema = $is_cinema;
                $lapumia->movie_name = $movie_name;
                return $lapumia;
            } else if (Str::contains($link_site, "comandotorrentshd")) {
                $bludv = new Bludv($link_site);
                $themovie = new TheMovieDB($id_themovie, $is_serie);
                $imdb = new IMDB($id_imdb);
                $bludv->theMovieDB = $themovie;
                $bludv->imdb = $imdb;
                $bludv->is_serie = $is_serie;
                $bludv->is_cinema = $is_cinema;
                $bludv->movie_name = $movie_name;
                return $bludv;
            } else if (Str::contains($link_site, "comandotorrents.org")) {
                $comando = new ComandoTorrent($link_site);
                $themovie = new TheMovieDB($id_themovie, $is_serie);
                $imdb = new IMDB($id_imdb);
                $comando->theMovieDB = $themovie;
                $comando->imdb = $imdb;
                $comando->is_serie = $is_serie;
                $comando->is_cinema = $is_cinema;
                $comando->movie_name = $movie_name;
                return $comando;
            } else {
                return null;
            }
        }

        public function procura_imagem()
        {
            return view("screens.movies.procura-imagem");
        }

        public function action_procurar_imagem(Request $request)
        {
            set_time_limit(0);
            try {
                $b = new BuscaImagemOffline($request->post("page_start"), $request->post("page_end"));
                $result = $b->start();
                return json_encode(["result" => $result]);
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return json_encode(["result" => $ex]);
            }
        }

        public function tela_troca_imagem()
        {
            return view("screens.movies.trocaimagem-filme");
        }

        public function trocar_imagem(Request $request)
        {
            set_time_limit(0);
            $postagem_erro_id = "0";
            $progresso_nome = $request->post("progress");
            $usar_imdb = $request->has("imdb");
            Session::put($progresso_nome, "Iniciando");
            Session::save();
            $postagem_id = $request->post("postagem_id");
            $endpoint = "https://filmestorrent.vip/xmlrpc.php";
            $wpClient = new WordpressClient();
            $wpClient->setCredentials($endpoint, 'synchronized', 'HN!2)ank$mn%E$GFE3Kd495F');
            if (!empty($postagem_id)) {
                $postagem_ids = explode(",", $postagem_id);
                foreach ($postagem_ids as $post_id) {
                    $postagem_erro_id = $post_id;
                    try {
                       $result =  $this->imdb_troca_imagem($post_id, $wpClient, $progresso_nome);
                       if (!$result){
                           $this->themovie_troca_imagem($post_id, $wpClient, $progresso_nome);
                       }
                    } catch (\Exception $ex) {
                        \Log::info("Erro post: " . $post_id);
                        \Log::error($ex);
                    }
                }
            }
            $msg["msg"] = "sucesso";
            return json_encode($msg);
        }

        private function imdb_troca_imagem($post_id, WordpressClient $wpClient, $progresso_nome)
        {
            if (!empty(trim($post_id)) && !is_null($post_id)) {
                $content = trim($wpClient->getPost($post_id)["post_content"]);
                try{
                    if (!empty($content)) {
                        $dom = HtmlDomParser::str_get_html($content);
                        $total = $dom->find("a[href*='imdb']");
                        if (count($total) > 0) {
                            $imdb_link = $dom->findOne("a[href*='imdb']")->getAttribute("href");
                            $id_imdb = FuncoesUteis::pegarIDLinkIMDB($imdb_link);
                            $imdb = new IMDB($id_imdb);
                            $imdb->pegar_imagem_capa_e_nome();
                            $imagem_url = $imdb->link_capa;
                            $nome = $imdb->titulo;
                            $nome = FuncoesUteis::limpar_caracteres_especiais($nome);
                            $nome_pronto = $nome . "filmestorrent-vip.png";
                            $dir_separator = DIRECTORY_SEPARATOR;
                            $img = "img" . $dir_separator . "baixadas" . $dir_separator . "$nome_pronto";
                            FuncoesUteis::baixar_imagem($imagem_url, $img);

                            Imagens::colocar_logo_na_imagem(Sites::FILMESTORRENT_VIP,
                                public_path("img" . $dir_separator . "baixadas" . $dir_separator . "$nome_pronto"),
                                public_path("img" . $dir_separator . "baixadas" . $dir_separator . "$nome_pronto"));
                            $path_imagem = ".." . $dir_separator . "public" . $dir_separator . "img" . $dir_separator . "baixadas" . $dir_separator . "$nome_pronto";
                            $mime = 'image/png';
                            $data = file_get_contents($path_imagem);
                            $resultado = $wpClient->uploadFile($nome_pronto, $mime, $data, true);
                            $url = $resultado["url"];
                            $dom = HtmlDomParser::str_get_html($content);
                            $dom->findOne("img")->setAttribute("src", $url);
                            $content = $dom->html();
                            $new_content = [
                                "post_content" => $content, "post_thumbnail" => (int)$resultado["id"]
                            ];
                            try {
                                if ($wpClient->editPost($post_id, $new_content)) {
                                    Session::put($progresso_nome . "-filme", $post_id);
                                    Session::save();
                                    return true;
                                } else {
                                    \Log::info("erro na postagem: ".$post_id);
                                    return false;
                                }
                            } catch (\Exception $ex) {
                                \Log::error($ex);
                                \Log::info("Erro com wordpress, post id: " . $post_id);
                                return false;
                            }
                        } else {
                            \Log::info("Não encontrou IMDB post_id: " . $post_id);
                            return false;
                        }
                    }
                }catch (\Throwable $ex){
                    \Log::error($ex);
                    \Log::info("Houve um erro ao usar IMDB para atualizar o post: ".$post_id);
                    return false;
                }
            } else {
                \Log::info("post ID vázio");
                return false;
            }
            return false;
        }

        private function themovie_troca_imagem($post_id, WordpressClient $wpClient, $progresso_nome)
        {
            if (!empty(trim($post_id)) && !is_null($post_id)) {
                $content = $wpClient->getPost($post_id)["post_content"];
                if (!empty(trim($content))) {
                    if (Str::contains($content, "Título:") || Str::contains($content, "Baixar Filme") || Str::contains($content, "Baixar Série")) {
                        $re = '/<b>Título:<\/b>(.*)|<b>Baixar Filme:<\/b>(.*)|<b>Baixar Série:<\/b>(.*)/m';
                        $resultado = FuncoesUteis::useRegex($re, $content);
                        if (!is_null($resultado)) {
                            try {
                                $nome = empty($resultado[1]) ? empty($resultado[2]) ? $resultado[3] : $resultado[2] : $resultado[1];
                            } catch (\Exception $ex) {
                                $nome = "";
                                \Log::error($ex);
                                \Log::info("Não encontrou dados: " . $post_id);
                            }
                            if (!empty($nome)) {
                                $id = TheMovieDB::procurar_filme_ou_serie($nome)["id"];
                                if (!empty($id)) {
                                    $tb = new TheMovieDB($id, false);
                                    $tb->pegar_dados();
                                    if (!is_null($tb->imagem_capa_link)) {
                                        $nome = FuncoesUteis::limpar_caracteres_especiais($nome);
                                        $nome_pronto = $nome . "filmestorrent-vip.png";
                                        $dir_separator = DIRECTORY_SEPARATOR;
                                        $img = "img" . $dir_separator . "baixadas" . $dir_separator . "$nome_pronto";
                                        FuncoesUteis::baixar_imagem($tb->imagem_capa_link, $img);

                                        Imagens::colocar_logo_na_imagem(Sites::FILMESTORRENT_VIP,
                                            public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . $nome_pronto),
                                            public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . $nome_pronto));
                                        $path_imagem = ".." . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome_pronto";
                                        $mime = 'image/png';
                                        $data = file_get_contents($path_imagem);
                                        $resultado = $wpClient->uploadFile($nome_pronto, $mime, $data, true);
                                        $url = $resultado["url"];
                                        $dom = HtmlDomParser::str_get_html($content);
                                        $dom->findOne("img")->setAttribute("src", $url);
                                        $content = $dom->html();
                                        $new_content = [
                                            "post_content" => $content, "post_thumbnail" => (int)$resultado["id"]
                                        ];
                                        try {
                                            if ($wpClient->editPost($post_id, $new_content)) {
                                                Session::put($progresso_nome . "-filme", $post_id);
                                                Session::save();
                                            } else {
                                                $msg["msg"] = "erro na postagem: $post_id";
                                                return json_encode($msg);
                                            }
                                        } catch (\Exception $ex) {
                                            \Log::error($ex);
                                            \Log::info("Erro com wordpress, post id: " . $post_id);
                                        }
                                    } else {
                                        \Log::info("Não encontrado capa themovie, post: " . $post_id);
                                    }
                                } else {
                                    \Log::info("Themovie não encontrado para: " . $nome);
                                }

                            }
                        }

                    } else {
                        \Log::info("Não contem dados, post id: " . $post_id);
                    }
                } else {
                    \Log::info("Posta Vázio: " . $post_id);
                }
            } else {
                \Log::info("nulo");
            }
        }
    }
