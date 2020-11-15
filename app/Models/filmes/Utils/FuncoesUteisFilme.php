<?php


    namespace App\Models\filmes\Utils;


    use App\Enums\AudioFilme;
    use App\Enums\QualidadeLinkFilme;
    use App\Enums\Sites;
    use App\Models\filmes\Filme;
    use App\Models\filmes\TheMovieDB;
    use App\Models\IMDB;
    use App\Models\YouTube;
    use App\Utils\FuncoesUteis;
    use Exception;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str;
    use voku\helper\HtmlDomParser;

    class FuncoesUteisFilme
    {
        public function pegar_dados_e_verificar(HtmlDomParser $dom, array $multiselector, $remove_invalid = [], $default_return = "")
        {
            try {
                foreach ($multiselector as $selector) {
                    $find = $dom->findOneOrFalse($selector);
                    if ($find != false) {
                        if (!empty($remove_invalid)) {
                            return FuncoesUteis::multipleReplace($remove_invalid, "", $find->nextSibling()->text());
                        }
                        return trim($find->nextSibling()->text());
                    }
                }
                return $default_return;
            } catch (Exception $ex) {
                \Log::error($ex);
                return $default_return;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return $default_return;
            }
        }

        public static function pegar_dados_e_verificar_static(HtmlDomParser $dom, array $multiselector, $remove_invalid = [])
        {
            try {
                foreach ($multiselector as $selector) {
                    $find = $dom->findOneOrFalse($selector);
                    if ($find != false) {
                        if (!empty($remove_invalid)) {
                            return FuncoesUteis::multipleReplace($remove_invalid, "", $find->nextSibling()->text());
                        }
                        return trim($find->nextSibling()->text());
                    }
                }
                return "";
            } catch (Exception $ex) {
                \Log::error($ex);
                return "";
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return "";
            }
        }

        protected function preparar_dados_serie_ou_filme(Filme $filme)
        {
            $filme->filme_ou_serie = $filme->is_serie ? "Série" : "Filme";
            $filme->filme_ou_serie_texto = $filme->is_serie ? "Na Série" : "No Filme";
            if (!$filme->is_serie) {
                $filme->serie_anotacao = "";
            }
        }

        /**
         * @param $texto
         * @return string
         */
        protected function identificar_audiofilme($texto)
        {

            if (Str::contains($texto, "Português") && !Str::contains($texto, "Inglês")) {
                return AudioFilme::DUBLADO;
            } elseif (!Str::contains($texto, "Português") && Str::contains($texto, "Inglês")) {
                return AudioFilme::LEGENDADO;
            } elseif (!Str::contains($texto, "Português") && !Str::contains($texto, "Inglês")) {
                return AudioFilme::LEGENDADO;
            } else {
                return AudioFilme::DUAL_AUDIO;
            }
        }

        /**
         * @param $qualidade
         * @return mixed
         */
        protected function arrumar_qualidade($qualidade)
        {
            return FuncoesUteis::multipleReplace(["720p | 1080p","720p","1080p"," e",":","&nbsp;","2160p", "|"," ,"],'',$qualidade);
        }

        protected function identificar_audiofilme_texto($texto)
        {
            if (Str::contains($texto, "Legenda") || Str::contains($texto, "Legendado")) {
                return AudioFilme::LEGENDADO;
            } elseif (Str::contains($texto, "Dublado")) {
                return AudioFilme::DUBLADO;
            } else {
                return AudioFilme::DUAL_AUDIO;
            }
        }

        /**
         * @param $texto
         * @return string
         */
        protected function identificar_qualidade_link_por_imagem($texto)
        {
            if (Str::contains($texto, "1080p")) {
                return QualidadeLinkFilme::LINK_1080P;
            } elseif (Str::contains(mb_strtolower($texto), "4k") || Str::contains(mb_strtolower($texto), "2160p")) {
                return QualidadeLinkFilme::LINK_4K;
            } else {
                return QualidadeLinkFilme::LINK_720P;
            }
        }

        protected function verificar_e_usar_themoviedb(TheMovieDB $theMovieDB, Filme $filme)
        {
            if (!empty($theMovieDB)) {
                $filme->id_the_movie = $theMovieDB->pegar_id();
                $theMovieDB->pegar_dados();
                $filme->sinopse = $theMovieDB->sinopse;
                $filme->url_imagem_the_movie = $theMovieDB->imagem_capa_link;
                if (empty($filme->titulo_original)) {
                    $filme->titulo_original = $theMovieDB->titulo;
                }
                if (empty($filme->titulo_traduzido)) {
                    $filme->titulo_traduzido = $theMovieDB->titulo;
                }
                if (empty($filme->titulo)) {
                    $filme->titulo = $theMovieDB->titulo;
                }
            }
        }

        protected function verificar_se_e_imagem_cinema(Filme $filme)
        {
            if ($filme->is_cinema) {
                $texto_inicial = "<h3><span style='color: #00ff00;'>ESSE FILME TEM QUALIDADE GRAVADA EM CINEMA!</span></h3>" . $filme->sinopse;
                $filme->sinopse = $texto_inicial;
            }
        }

        protected function verificar_e_usar_imdb(IMDB $imdb, Filme $filme)
        {
            if (!empty($imdb)) {
                $imdb->pegar_dados_filme();
                $filme->nota_imdb = $imdb->nota;
            }
        }

        public function usar_link_youtube(Filme $filme)
        {
            $links_trailer = YouTube::pesquisar_trailer_filme_embed($filme->titulo_original);
            if (count($links_trailer) > 0) {
                $filme->trailer_1 = $links_trailer[0]["embed"];
                $filme->trailer_2 = $links_trailer[1]["embed"];
                $filme->trailer_3 = $links_trailer[2]["id"];
                $filme->trailer_4 = $links_trailer[3]["embed"];
                $filme->trailer_5 = $links_trailer[4]["embed"];
                $filme->trailer_6 = $links_trailer[5]["id"];
                $filme->trailer_7 = $links_trailer[0]["id"];
            } else {
                $filme->trailer_1 = "";
                $filme->trailer_2 = "";
                $filme->trailer_3 = "";
                $filme->trailer_4 = "";
                $filme->trailer_5 = "";
                $filme->trailer_6 = "";
                $filme->trailer_7 = "";
            }

        }

        protected function preparar_imagens(Filme $filme)
        {
            $img = "img/temp2.png";
            $nome = FuncoesUteis::limpar_caracteres_especiais($this->titulo);
            $nome_pronto = $nome . "-filmestorrent-vip.png";
            $img_filmestorrent = "img/baixadas/" . $nome_pronto;
            FuncoesUteis::baixar_imagem($filme->url_imagem_the_movie, $img);
            FuncoesUteis::baixar_imagem($filme->url_imagem_the_movie, $img_filmestorrent);
        }

        protected function preparar_categorias(Filme $filme)
        {
            $generos = preg_replace('/\s+/', '', $filme->generos);
            $generos_separados = array_map('trim', explode(",", $generos));
            $generos_separados[] = $filme->ano_lancamento;
            $this->prepara_qualidade_para_categoria($generos_separados,$filme->qualidade);
            $generos_separados[] = "720p";
            $generos_separados[] = $filme->is_serie ? "Séries" : "Filmes";
            if (Str::contains($filme->qualidade_original, "1080p")) {
                $generos_separados[] = "1080p";
            }
            $filme->categorias_separadas = array_filter($generos_separados);
        }

        protected function prepara_qualidade_para_categoria(&$categoria,$qualidade){
            $resultado = preg_split('/\ /',$qualidade);
            foreach ($resultado as $r){
                $categoria[] = $r;
            }
        }

        protected function preparar_titulo_sites(Filme $filme)
        {
            if ($filme->is_serie) {
                $filme->titulo_filmes_via_torrent_info = "Download " . $filme->titulo . " " . $filme->serie_temporada . "ª Temporada Torrent – " . $filme->qualidade . " " . $this->get_audio_formatado_para_titulo($filme->audioFilme);
                $filme->titulo_filmes_via_torrent_org = "Torrent " . $filme->titulo . " - " . $filme->serie_temporada . "ª Temporada" . $filme->ano_lancamento . " – " . $filme->qualidade . " " . $this->get_audio_formatado_para_titulo($filme->audioFilme);
                $filme->titulo_piratefilmes = "Download " . $filme->titulo . " - " . $filme->serie_temporada . "ª Temporada – " . $filme->qualidade . " " . $this->get_audio_formatado_para_titulo($filme->audioFilme) . " " . $filme->ano_lancamento . " Torrent";
                $filme->titulo_filmesvip = $filme->titulo . " " . $filme->serie_temporada . "ª Temporada";
            } else {
                $filme->titulo_filmes_via_torrent_info = $filme->titulo . " - " . $filme->qualidade_original . " Torrent " . $filme->ano_lancamento . " " . $this->get_audio_formatado_para_titulo($filme->audioFilme) . " Download";
                $filme->titulo_piratefilmes = $filme->titulo . " - " . $filme->qualidade_original . " " . $this->get_audio_formatado_para_titulo($filme->audioFilme) . " Torrent (" . $filme->ano_lancamento . ")";
                $filme->titulo_filmes_via_torrent_org = "Torrent " . $filme->titulo . " Download - " . $filme->qualidade_original . " " . $this->get_audio_formatado_para_titulo($filme->audioFilme) . " " . $filme->ano_lancamento;
                $filme->titulo_filmesvip = $filme->titulo;
                $filme->titulo_kfilme = $filme->titulo . " " . $this->prepara_titulo_kfilmes($filme->audioFilme);
            }

        }

        private function prepara_titulo_kfilmes($audio_filme)
        {
            if ($audio_filme != AudioFilme::LEGENDADO) {
                return "Dublado";
            }
            return "";
        }

        private function get_audio_formatado_para_titulo($audio_filme)
        {
            if ($audio_filme == AudioFilme::DUAL_AUDIO) {
                return $audio_filme . " / Dublado";
            } else {
                return $audio_filme;
            }
        }

        public static function retornar_tipo_audio($audioFilme)
        {
            switch ($audioFilme) {
                case AudioFilme::DUBLADO:
                    return "Dublado";
                case AudioFilme::LEGENDADO:
                    return "Legendado";
                case AudioFilme::DUAL_AUDIO:
                    return "Dublado e Legendado";
            }
        }

        public static function getOnlyYear($date)
        {
            $d = Carbon::createFromFormat("Y-m-d", $date);
            return $d->year;
        }
    }
