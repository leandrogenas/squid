<?php


    namespace App\Models\animes;


    use App\Enums\Sites;
    use App\Models\wordpress\WPress;
    use App\Utils\FuncoesUteis;
    use Corcel\Model\Post;
    use GuzzleHttp\Client;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\Log;

    class AnimePost
    {
        private $id_animesvip, $id_animesOrion, $nome_anime;
        private $cliente;
        private $wordpress;
        public $houve_erro = false;
        public $log_erro = "";

        /**
         * PreparaDados constructor.
         * @param $id_animesvip
         * @param $id_animesOrion
         * @param $nome_anime
         * @param WPress $wordpress
         */
        public function __construct($id_animesvip, $id_animesOrion, $nome_anime, WPress $wordpress)
        {
            $this->id_animesvip = $id_animesvip;
            $this->id_animesOrion = $id_animesOrion;
            $this->nome_anime = $nome_anime;
            $this->wordpress = $wordpress;
            $this->cliente = new Client();
        }

        public function start($ep_start = 1, $ep_end = 2)
        {
            try {
                $this->log_erro = "";
                $this->houve_erro = false;
                $episodios_orion = $this->pegarEpisodiosOrion($ep_start, $ep_end);
                $episodios_vip = $this->pegarEpisodiosVip($ep_start, $ep_end);
                $dados_postagem = $this->prepara_lista_atualizar($episodios_orion, $episodios_vip);
                $this->atualizar_postagem($dados_postagem);
                return true;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log_erro .= "Houve um erro: " . $ex->getMessage() . "\n";
                $this->houve_erro = true;
                return false;
            }
        }

        private function atualizar_postagem(array $dados)
        {
            foreach ($dados as $key => $values) {
                try {
                    if ($values["episodio_player_2_id"] > 0) {
                        $campo_player_2 = ["id" => $values["episodio_player_2_id"], "key" => "episodio_player_2", "value" => $values["episodio_player_2"]];
                    } else {
                        $campo_player_2 = ["key" => "episodio_player_2", "value" => $values["episodio_player_2"]];
                    }
                    $content["custom_fields"] = [
                        ["id" => $values["episodio_mp4_id"], "key" => "episodio_mp4", "value" => $values["episodio_mp4"]],
                        $campo_player_2
                    ];
                    $this->wordpress->editPostPorSite(Sites::ANIMESONLINE_VIP, $key, $content, false);
                } catch (\Throwable $ex) {
                    \Log::error($ex);
                    \Log::info("Não foi possível atualizar a postagem ID: " . $key);
                    $this->log_erro .= "Não foi possível atualizar a postagem ID: " . $key . "\n";
                    $this->houve_erro = true;
                }
            }
        }

        /**
         * @param PostAnimesOrion[] $episodios_orion
         * @param Post[] $episodios_vip
         * @return array
         */
        private function prepara_lista_atualizar($episodios_orion, $episodios_vip)
        {
            $dados = [];
            $link_api_vip = Config::get("sync.link_servidor_google");
            foreach ($episodios_vip as $key => $value){
                if(isset($episodios_orion[$key])){
                    try{
                        $post_orion = $episodios_orion[$key];
                        $post_vip = $value;
                        $player_backup = $post_orion->meta->episodio_mp4;
                        $player_api_original = $post_orion->meta->link_blogger;
                        $id_player = explode("list=", $player_api_original);
                        if(isset($id_player[1])){
                            $player_api = $link_api_vip . $id_player[1];
                        }else{
                            $player_api = "";
                        }
                        if (empty($player_backup)) {
                            if (!empty($player_api)) {
                                try {
                                    $headers = get_headers($player_api_original);
                                    $m_array = preg_grep('/(.*)googlevideo(.*)/', $headers);
                                    $location = trim(str_replace("Location:","",array_values($m_array)[0]));
                                    $player_backup = str_replace("Location: ", "", $location);
                                    $player_backup = $this->baixarVideoBackup($player_backup, $this->nome_anime, $post_orion->meta->episodio_ep, $player_api);
                                } catch (\Throwable $ex) {
                                    \Log::error($ex);
                                    \Log::info("Não foi possível pegar o location na api: " . $player_api_original);
                                    $this->houve_erro = true;
                                    $this->log_erro .= "Não foi possível pegar o location na API: " . $player_api_original . " na postagem animesorion: " . $post_orion->ID . " Foi utilizado a API no lugar\n";
                                    $player_backup = $player_api;
                                }
                            }
                        } else {
                            $player_backup = $this->baixarVideoBackup($player_backup, $this->nome_anime, $post_orion->meta->episodio_ep, $player_api);
                        }
                        $meta_id_episodio_mp4 = $post_vip->meta()->where("meta_key", "episodio_mp4")->first(["meta_id"])->meta_id;
                        $meta_player_2 = $post_vip->meta()->where("meta_key", "episodio_player_2")->first(["meta_id"]);
                        $meta_id_player_2 = 0;
                        if ($meta_player_2) {
                            $meta_id_player_2 = $meta_player_2->meta_id;
                        }
                        $dados[$post_vip->ID] = ["episodio_mp4" => $player_backup, "episodio_mp4_id" => $meta_id_episodio_mp4, 'episodio_player_2' => $player_api, "episodio_player_2_id" => $meta_id_player_2];
                    }catch (\Throwable $ex){
                        \Log::error($ex);
                        $this->houve_erro = true;
                        $this->log_erro .= "Houve um erro: " . $ex->getMessage() . " com episódio: ".$key."\n";
                    }
                }else{
                    \Log::info("Não foi encontrado episódio no orion: ".$key);
                    $this->houve_erro = true;
                    $this->log_erro .= "Não foi encontrado episódio no orion: ".$key."\n";
                }
            }
            return $dados;
        }

        private function baixarVideoBackup($link_backup, $anime, $episodio, $player_api)
        {
            try {
                $response = $this->cliente->request("POST", Config::get("sync.link_servidor_download"), [
                    'form_params' => [
                        'user' => 'terminal',
                        'password' => 'nutela',
                        'anime' => FuncoesUteis::limpar_caracteres_especiais($anime),
                        'episodio_numero' => $episodio,
                        'link' => $link_backup
                    ]
                ]);
                $resultado = $response->getBody()->getContents();
                $resultado = json_decode($resultado);
                if ($resultado->logado) {
                    return $resultado->link;
                } else {
                    \Log::info("Não foi possível logar link: " . $link_backup . " no anime: " . $anime . " no episódio: " . $episodio . " foi utilizado a API para resolver");
                    $this->houve_erro = true;
                    $this->log_erro .= "Não foi possível logar link: " . $link_backup . " no anime: " . $anime . " no episódio: " . $episodio . " foi utilizado a API para resolver\n";
                    return $player_api;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                \Log::info("houve um erro ao tentar fazer upload do link: " . $link_backup . " no anime: " . $anime . " no episódio: " . $episodio . " foi utilizado a API para resolver");
                $this->houve_erro = true;
                $this->log_erro .= "houve um erro ao tentar fazer upload do link: " . $link_backup . " no anime: " . $anime . " no episódio: " . $episodio . " foi utilizado a API para resolver\n";
                return $player_api;
            }
        }

        private function pegarEpisodiosOrion($ep_start, $ep_end)
        {
            $episodios = [];
            $posts_orion = PostAnimesOrion::published()->hasMeta("episodio_anime", $this->id_animesOrion)->hasMeta("episodio_ep", $ep_start, ">=")->hasMeta("episodio_ep", $ep_end, "<=")->get();
            try {
                foreach ($posts_orion as $post) {
                    $episodios[ltrim($post->meta->episodio_ep, '0')] = $post;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->houve_erro = true;
                $this->log_erro .= "Houve um erro ao pegar episódios animesorion: " . $ex->getMessage() . "\n";
            }
            return $episodios;
        }

        private function pegarEpisodiosVip($ep_start, $ep_end)
        {
            $episodios = [];
            $posts_vips = Post::published()->hasMeta("episodio_anime", $this->id_animesvip)->hasMeta("episodio_ep", $ep_start, ">=")->hasMeta("episodio_ep", $ep_end, "<=")->get();
            try {
                foreach ($posts_vips as $post) {
                    $episodios[ltrim($post->meta->episodio_ep, '0')] = $post;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->houve_erro = true;
                $this->log_erro .= "Houve um erro ao pegar episódios animesvip: " . $ex->getMessage() . "\n";
            }
            return $episodios;
        }

        public static function buscaAnimeOrion($nome_anime)
        {
            $posts = PostAnimesOrion::published()->type("post")->hasMetaLike("anime_titulo", "%" . $nome_anime . "%")->get();
            try {
                $encontrado["encontrado"] = true;
                foreach ($posts as $post){
                    $encontrado["lista"][] = ["id" => $post->ID, "title" => $post->title, "anime" => $post->meta->anime_titulo];
                }
                return $encontrado;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $encontrado["encontrado"] = true;
                return $encontrado;
            }

        }
    }
