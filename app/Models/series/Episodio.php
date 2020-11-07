<?php


    namespace App\Models\series;


    use App\Enums\Sites;
    use App\Models\filmes\TheMovieDB;
    use App\Models\Imagens;
    use App\Utils\FuncoesUteis;
    use Illuminate\Support\Str;
    use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
    use Stichoza\GoogleTranslate\GoogleTranslate;

    class Episodio
    {
        public $titulo, $temporada, $episodio, $tipo, $descricao, $link, $imagem_themovie, $imagem_pronta, $content, $nome_episodio, $link_original;
        /**
         * @var Temporadas
         */
        public $temporada_class;



        public function getTituloPronto()
        {
            return $this->titulo . " " . $this->temporada . "ª Temporada Episódio " . $this->episodio . " " . Str::ucfirst(strtolower($this->tipo));
        }

        public function getLinkPronto()
        {
            if (FuncoesUteis::identificar_links_normais($this->link)) {
                return $this->link;
            } else {
                return \Config::get("sync.link_servidor_google") . "" . $this->link;
            }
        }

        public function prepara_informacao(TheMovieDB $theMovieDB)
        {
            try {
                $resultado = $theMovieDB->getEpisodioDetalhes($this->temporada, $this->episodio);
                $this->descricao = $this->preparar_descricao($theMovieDB,$resultado);
                $this->nome_episodio = $resultado->name;
                $this->imagem_themovie = "https://image.tmdb.org/t/p/w454_and_h254_bestv2" . $resultado->still_path;
                if (empty($this->descricao) || is_null($this->descricao)) {
                    $this->descricao = FuncoesUteis::multipleReplace(['"'], '', $this->temporada_class->descricao);
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->descricao = FuncoesUteis::multipleReplace(['"'], '', $this->temporada_class->descricao);;
                $this->imagem_themovie = "https://image.tmdb.org/t/p/w454_and_h254_bestv2" . $theMovieDB->imagem_fundo_sem_link;
                $this->nome_episodio = "";
            }
        }

        private function preparar_descricao(TheMovieDB $theMovieDB, $resultado)
        {
            if (empty($resultado->overview)) {
                $google_tradutor = new GoogleTranslate("en");
                $google_tradutor->setTarget("pt-br");
                $resultado_ingles = $theMovieDB->getEpisodioDetalhes($this->temporada, $this->episodio, "en");
                $traduzir = $google_tradutor->translate($resultado_ingles->overview);
                return FuncoesUteis::multipleReplace(['"'], "", $traduzir);
            }
            return $resultado->overview;
        }

        public function baixar_imagem()
        {
            try {
                $link_pronto = $this->link_original;
                $numero_aletorio = random_int(1, 20);
                $minuto = str_pad($numero_aletorio, 2, "0", STR_PAD_LEFT);
                $nome = $this->getNomeImagem();
                if (!Imagens::tirar_print("00:$minuto:00", "img/baixadas/" . $nome, $link_pronto)) {
                    FuncoesUteis::baixar_imagem($this->imagem_themovie, "img/baixadas/" . $nome);
                }
                ImageOptimizer::optimize("img/baixadas/" . $nome);
                return true;
            } catch (\Throwable $ex) {
                return false;
            }
        }

        private function getNomeImagem()
        {
            return $this->preparar_imagens_series($this->temporada . "-temporada-" . "-episodio-" . $this->episodio . "-seriesonline-pro.jpeg");
        }

        public function preparar_imagens_por_site($site)
        {
            $this->colocar_logo_imagem($this->getNomeImagem(), $site);
        }

        private function preparar_imagens_series($extensao_nome)
        {
            $nome = FuncoesUteis::limpar_caracteres_especiais($this->titulo);
            $nome_pronto = $nome . $extensao_nome;
            $this->imagem_pronta = $nome_pronto;
            return $nome_pronto;
        }

        private function colocar_logo_imagem($nome, $site)
        {
            Imagens::colocar_logo_somente_serie($site, public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome"),
                public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome"), false);
        }

        public function getCategoriaEpisodio()
        {
            $complemento = $this->tipo == "LEGENDADO" ? "legendado-" : "";
            $categoria = [];
            $categoria[] = strtolower($this->titulo . "-" . $complemento . "" . $this->temporada . "-temporada");
            return $categoria;
        }
    }
