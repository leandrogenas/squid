<?php


    namespace App\Models\animes;

    use App\Enums\AnimeTipo;
    use App\Models\Imagens;
    use App\Utils\FuncoesUteis;
    use Arcanedev\LogViewer\Entities\Log;
    use GuzzleHttp\Client;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Str;
    use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

    /**
     * Class Animes
     * @property  Episodio[] episodios
     * @package App\Models\animes
     */
    abstract class Animes
    {
        public $titulo, $content, $post_vip_id, $link, $link_capa, $tipo, $generos, $anime_letra, $descricao, $data = "2020", $imagem_pronta, $titulo_original, $tipo_anime, $link_imagem_themovie;
        public $cliente;
        public $episodios;
        public $log = "";

        /**
         * Animes constructor.
         * @param $link
         */
        public function __construct($link)
        {
            $this->link = $link;
            $this->tipo_anime = AnimeTipo::EPISODIO;
            $this->cliente = new Client();
        }


        public abstract function carregar($episodio_start = 1, $episodio_end = 2);

        protected function arrumar_descricao($descricao,$nome_anime){
            $descricao = FuncoesUteis::multipleReplace(["class=\"post-titulo\"","class='post-titulo'"],"",$descricao);
            return $descricao;
        }

        public function carregar_orion($id, $episodio_start = 1, $episodio_end = 2)
        {
            try {
                $post = PostAnimesOrion::find($id);
                if ($post) {
//                    $titulo_anime = trim(FuncoesUteis::remover_palavras_animes($post->meta->anime_titulo));
//                    $this->titulo = FuncoesUteis::multipleReplace(["Legendado","legendado"],"",$post->post_title);
                    $titulo_anime = trim(FuncoesUteis::remover_palavras_animes($this->titulo));
                    $this->titulo = FuncoesUteis::multipleReplace(["Legendado","legendado"],"",$this->titulo);
                    $this->tipo = $post->meta->anime_tipo;
                    $this->descricao = $this->arrumar_descricao($post->post_content,$titulo_anime);
                    $categorias = [];
                    foreach ($post->taxonomies()->get() as $taxonimie) {
                        $categorias[] = $taxonimie->term()->first()->name;
                    }
                    $this->generos = implode(",", $categorias);
                    $this->titulo_original = $titulo_anime;
                    $this->link_capa = $post->attachment()->first()->guid;
                    $this->anime_letra = $post->meta->anime_letra;
                    $this->data = $post->meta->anime_data;
                    $this->carregar_episodios_orion($post->ID,$episodio_start,$episodio_end);
                } else {
                    $this->log .= "Não foi encontrado o post em animesorion, POST ID: " . $id . "\n";
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Não foi possível carregar os dados de animesorion, POST ID: " . $id . " Erro: ".$ex->getMessage()."\n";
            }
        }
        protected function carregar_episodios_orion($id,$ep_start,$ep_end){
            $link_api_vip = Config::get("sync.link_servidor_google");
            $posts_orion = PostAnimesOrion::published()->hasMeta("episodio_anime", $id)->hasMeta("episodio_ep", $ep_start, ">=")->hasMeta("episodio_ep", $ep_end, "<=")->get();
            try{
                foreach ($posts_orion as $post) {
                    $episodio = ltrim($post->meta->episodio_ep, '0');
                    $player_backup = $post->meta->episodio_mp4;
                    $player_api_original = $post->meta->link_blogger;
                    $id_player = explode("list=", $player_api_original);
                    if(isset($id_player[1])){
                        $player_api = $link_api_vip . $id_player[1];
                    }else{
                        $player_api = "";
                    }
                    $fazer_download_do_link = false;
                    if (empty($player_backup)) {
                        if (!empty($player_api_original)) {
                            try {
                                $headers = get_headers($player_api_original);
                                $m_array = preg_grep('/(.*)googlevideo(.*)/', $headers);
                                $location = trim(str_replace("Location:","",array_values($m_array)[0]));
                                $player_backup = str_replace("Location: ", "", $location);
                                $fazer_download_do_link = true;
                            } catch (\Throwable $ex) {
                                \Log::error($ex);
                                \Log::info("Não foi possível pegar o location na api: " . $player_api_original);
                                $this->log .= "Não foi possível pegar o location na API: " . $player_api_original . " na postagem animesorion: " . $post->ID . " Foi utilizado a API no lugar\n";
                                $player_backup = $player_api;
                            }
                        }
                    }else{
                        $fazer_download_do_link = true;
                    }
                    $data = $post->post_date;
                    $dia_aleatorio  = random_int(1,10);
                    $data_final = $data->addDays($dia_aleatorio)->toDateTime();;
                    $ep = new Episodio();
                    $ep->use_orion = true;
                    $ep->link_download = $player_backup;
                    $ep->link_player_2 = $player_api;
                    $ep->fazer_download_do_link = $fazer_download_do_link;
                    $ep->episodio_letra = $this->anime_letra;
                    $ep->link_google = $player_api_original;
                    $ep->episodio_numero = $episodio;
                    $ep->episodio_tipo = $this->tipo;
                    $ep->titulo = $this->prepara_titulo_episodio($this->titulo,$episodio);
                    $ep->descricao = "Assistir ".$ep->titulo;
                    $ep->nome_anime = $this->titulo_original;
                    $ep->cliente = $this->cliente;
                    $ep->episodio_tempo = $post->meta->episodio_tempo;
                    $ep->link_imagem_orion = $post->meta->episodio_capa;
                    $ep->post_date = $data_final;
                    $ep->titulo_anime = $this->titulo;
                    $this->episodios[] = $ep;
                }
            }catch (\Throwable $ex){
                \Log::error($ex);
                $this->log .= "Houve um erro ao pegar episódios animesorion: " . $ex->getMessage() . "\n";
            }
        }

        public function getGeneros()
        {
            return array_map('ucfirst', explode(",", $this->generos));
        }

        protected function prepara_titulo_episodio($titulo,$episodio_numero){
            $titulo = FuncoesUteis::multipleReplace(["Legendado","legendado"],"",$titulo);
            return $titulo." - Episódio ".$episodio_numero;
        }

        protected function preparatitulo($titulo)
        {
            if ($this->tipo_anime == AnimeTipo::EPISODIO) {
                if ($this->tipo === "Dublado") {
                    if (!Str::contains(strtolower($titulo), "dublado")) {
                        return $titulo . " Dublado";
                    }
                }
                return $titulo;
            } else {
                return $this->verificar_titulo(strtolower($titulo)) ? $titulo : $titulo . " " . $this->tipo_anime;
            }
        }

        protected function verificar_titulo($titulo)
        {
            $ajustar = ["movie", "ova", "filme"];
            foreach ($ajustar as $v) {
                if (Str::contains($titulo, $v)) {
                    return true;
                }
            }
            return false;
        }

        public function baixarImagem()
        {
            try {
                $use_themovie = false;
                if (is_null($this->link_imagem_themovie)) {
                    $link_imagem = $this->link_capa;
                } else {
                    $link_imagem = $this->link_imagem_themovie;
                    $use_themovie = true;
                }
                $img = "img/temp.png";
                FuncoesUteis::baixar_imagem($link_imagem, $img);
                $nome = FuncoesUteis::limpar_caracteres_especiais($this->titulo) . "-animesonlinevip.jpeg";
                Imagens::colocar_logo_anime(public_path("img" . DIRECTORY_SEPARATOR . "temp.png"),
                    public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome"), true, $use_themovie);
                $this->imagem_pronta = $nome;
                ImageOptimizer::optimize("img/baixadas/".$nome);
                return true;
            } catch (\Throwable $ex) {
                $this->log .= "Houve um erro ao baixar imagem de capa do anime: " . $this->titulo . "\n";
                \Log::error($ex);
                return false;
            }
        }
    }
