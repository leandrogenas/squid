<?php


    namespace App\Models\animes;


    use App\Models\Imagens;
    use App\Utils\FuncoesUteis;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Str;
    use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
    use Symfony\Component\Process\Process;

    class Episodio
    {
        public $episodio_numero, $episodio_capa, $episodio_letra, $episodio_mp4, $episodio_tipo, $episodio_tempo, $content, $link_download, $titulo, $descricao, $episodio_anime, $nome_anime, $imagem_pronta;
        public $log = "";
        public $cliente;
        //usados para animesorion
        public $use_orion = false;
        public $fazer_download_do_link = false;
        public $link_imagem_orion;
        public $post_date = null;
        public $link_player_2 = "";
        public $titulo_anime = "";
        public $link_google = "";

        public function preparaDados()
        {
            try {
                if (!$this->use_orion) {
                    $this->pegarDuracao($this->link_download);
                    $resultado = $this->baixarVideo($this->link_download);
                    if ($resultado != false) {
                        $this->episodio_mp4 = $resultado;
                        return true;
                    }
                    return false;
                } else {
                    if ($this->fazer_download_do_link) {
                        $this->pegarDuracao($this->link_download);
                        $resultado = $this->baixarVideo($this->link_download);
                        if ($resultado != false) {
                            $this->episodio_mp4 = $resultado;
                            return true;
                        }
                        return false;
                    } else {
                        $this->episodio_mp4 = $this->link_download;
                        return true;
                    }
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve erro ao preparar dados do episódio em " . $this->nome_anime . " no episódio " . $this->episodio_numero . ", mas será continuado para a etapa de imagem, erro: " . $ex->getMessage() . "\n";
                return false;
            }
        }

        private function pegarDuracao($link_download)
        {
            try {
                $sistema = PHP_OS;
                if ($sistema === "Linux") {
                    $script = 'ffmpeg -headers "Referer: https://animesvision.biz/" -i "' . $link_download . '" 2>&1 | grep Duration';
                } else {
                    $script = 'ffmpeg -headers "Referer: https://animesvision.biz/" -i "' . $link_download . '" 2>&1 | find "Duration"';
                }
                $p = new Process($script);
                $p->run();
                $resultado = $p->getOutput();
                $re = '/Duration:(.*?)\./m';
                preg_match_all($re, $resultado, $matches, PREG_SET_ORDER, 0);
                if (isset($matches[0][1])) {
                    $this->episodio_tempo = ltrim(trim($matches[0][1]), '0:');
                } else {
                    $this->episodio_tempo = "24:00";
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve erro ao pegar duração do episódio em " . $this->nome_anime . " no episódio " . $this->episodio_numero . " erro: " . $ex->getMessage() . "\n";
                $this->episodio_tempo = "24:00";
            }
        }

        public function baixar_imagem()
        {
            try {
                return $this->tirar_print_or_baixar($this->link_download);
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve erro ao pegar imagem do episódio em " . $this->nome_anime . " no episódio " . $this->episodio_numero . ", será utilizado link da API! Erro: " . $ex->getMessage() . "\n";
            }
            try {
                if (!empty($this->link_google)) {
                    try {
                        $headers = get_headers($this->link_google);
                        $m_array = preg_grep('/(.*)googlevideo(.*)/', $headers);
                        $location = trim(str_replace("Location:","",array_values($m_array)[0]));
                        $this->link_google = str_replace("Location: ", "", $location);
                    } catch (\Throwable $ex) {
                        $this->log .= "Não foi possível pegar o link do google! link: " . $this->link_google . " em ".$this->titulo;
                        return false;
                    }
                    $this->pegarDuracao($this->link_google);
                    $resultado = $this->baixarVideo($this->link_google);
                    if ($resultado != false) {
                        $this->episodio_mp4 = $resultado;
                    }
                    return $this->tirar_print_or_baixar($this->link_google);
                } else {
                    $this->log .= "Link do google vázio em: " . $this->nome_anime . " no episódio " . $this->episodio_numero . "\n";
                    return false;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve erro ao pegar imagem do episódio em " . $this->nome_anime . " no episódio " . $this->episodio_numero . " Erro: " . $ex->getMessage() . "\n";
                return false;
            }
        }

        private function tirar_print_or_baixar($link_download)
        {
            $nome = FuncoesUteis::limpar_caracteres_especiais($this->nome_anime) . "-episodio-" . $this->episodio_numero . "-animesonlinevip.jpeg";
            if (is_null($this->link_imagem_orion) || $this->fazer_download_do_link) {
                $numero_aletorio = random_int(3, 10);
                $minuto = str_pad($numero_aletorio, 2, "0", STR_PAD_LEFT);
                if ($this->tirar_print("00:$minuto:00", "img/baixadas/" . $nome, $link_download)) {
                    $numero_aletorio = random_int(10, 59);
                    if ($this->tirar_print("00:01:$numero_aletorio", "img/baixadas/" . $nome, $link_download)) {
                        $this->log .= "Não foi possivel tirar print da imagem em" . $this->nome_anime . " no episódio " . $this->episodio_numero . "\n";
                        return false;
                    }
                }
            } else {
                $imagem = "img/baixadas/" . $nome;
                FuncoesUteis::baixar_imagem($this->link_imagem_orion, $imagem);
            }
            Imagens::colocar_logo_anime(public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome"),
                public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome"), false);
            $this->imagem_pronta = $nome;
            ImageOptimizer::optimize("img/baixadas/".$nome);
            return true;
        }

        private function tirar_print($time = "00:10:00", $imagem_url = "img/temp.png", $link_download = "")
        {
            $script = 'ffmpeg -headers "Referer: https://animesvision.biz/" -ss ' . $time . ' -y -i "' . $link_download . '" -f image2 -vf scale=320:209 -vframes 1 "' . $imagem_url . '"';
            $p = new Process($script);
            $p->run();
            $resultado = $p->getErrorOutput();
            \Log::info($resultado);
            return Str::contains($resultado, "Output file is empty, nothing was encoded");
        }

        protected function baixarVideo($link_download)
        {
            try {
                $response = $this->cliente->request("POST", Config::get("sync.link_servidor_download"), [
                    'form_params' => [
                        'user' => 'terminal',
                        'password' => 'nutela',
                        'anime' => FuncoesUteis::limpar_caracteres_especiais($this->titulo_anime),
                        'episodio_numero' => FuncoesUteis::limpar_caracteres_especiais($this->episodio_numero),
                        'link' => $link_download
                    ]
                ]);
                $resultado = $response->getBody()->getContents();
                $resultado = json_decode($resultado);
                if ($resultado->logado) {
                    return $resultado->link;
                } else {
                    \Log::info("Não foi possível logar link: " . $this->link_download . " no anime: " . $this->nome_anime . " no episódio: " . $this->episodio_numero);
                    $this->log .= "Não foi possível logar link: " . $this->link_download . " no anime: " . $this->nome_anime . " no episódio: " . $this->episodio_numero . "\n";
                    return false;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                \Log::info("houve um erro ao tentar fazer upload do link: " . $this->link_download . " no anime: " . $this->nome_anime . " no episódio: " . $this->episodio_numero);
                $this->log .= "houve um erro ao tentar fazer upload do link: " . $this->link_download . " no anime: " . $this->nome_anime . " no episódio: " . $this->episodio_numero . "\n";
                return false;
            }
        }
    }
