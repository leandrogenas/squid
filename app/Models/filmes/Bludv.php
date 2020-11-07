<?php


    namespace App\Models\filmes;

    use App\Models\IMDB;
    use App\Utils\FuncoesUteis;
    use Illuminate\Support\Str;
    use voku\helper\HtmlDomParser;

    /**
     * Class Lapumia
     * @property string $link
     * @property HtmlDomParser $dom
     * @property TheMovieDB $theMovieDB
     * @property IMDB $imdb
     * @package App\Models\filmes
     */
    class Bludv extends Filme
    {

        private $link, $dom;

        public function __construct($link)
        {
            $this->link = $link;
        }

        private function carregar_site()
        {
            $this->dom = HtmlDomParser::file_get_html($this->link);
        }

        public function carregar_dados()
        {
            $this->carregar_site();
            if ($this->is_serie) {
                $this->carregar_dados_comun();
                $this->carregar_dados_serie();
            }
            self::preparar_titulo_sites($this);
            self::preparar_dados_serie_ou_filme($this);
        }

        private function arrumar_titulo_original($texto)
        {
            return FuncoesUteis::multipleReplace(['torrent', 'Torrent'], "", $texto);
        }

        private function carregar_dados_comun()
        {
            if (empty($this->movie_name)) {
                $this->titulo_original = $this->arrumar_titulo_original(self::pegar_dados_e_verificar($this->dom, ["b:contains(Original)", "strong:contains(Original)"]));
                $this->titulo_original = FuncoesUteis::limpar_caracteres_especiais_bludv($this->titulo_original);
                $this->titulo_traduzido = $this->titulo_original;
            } else {
                $this->movie_name = $this->arrumar_titulo_original($this->movie_name);
                $this->titulo_original = $this->movie_name;
                $this->titulo_traduzido = $this->movie_name;
                $this->titulo = $this->movie_name;
            }
            $this->generos = $this->pegar_generos();
            $this->audio = $this->removerEspacosEmBranco(self::pegar_dados_e_verificar($this->dom, ["b:contains(Idioma)", "b:contains('Áudio:')"]));
            $this->audioFilme = self::identificar_audiofilme($this->audio);
            $this->legenda = $this->removerEspacosEmBranco(self::pegar_dados_e_verificar($this->dom, ["b:contains(Legenda)"]));
            $this->qualidade = $this->removerEspacosEmBranco(self::pegar_dados_e_verificar($this->dom, ["b:contains(Qualidade)"]));
            $this->qualidade_original = $this->qualidade;
            $this->qualidade = self::arrumar_qualidade($this->qualidade);
            $this->formato = FuncoesUteis::ajustaFormato(self::pegar_dados_e_verificar($this->dom, ["b:contains('Extensão:')", "b:contains('Formato')"]));
            $this->tamanho = $this->removerEspacosEmBranco(self::pegar_dados_e_verificar($this->dom, ["b:contains(Tamanho)"]));
            $this->qualidade_audio = $this->removerEspacosEmBranco(self::pegar_dados_e_verificar($this->dom, ["strong:contains('Áudio:')", "b:contains('de Áudio:')", "b:contains('Qualidade de Áudio e Vídeo:')"]));
            $this->qualidade_video = $this->removerEspacosEmBranco(self::pegar_dados_e_verificar($this->dom, ["strong:contains('Vídeo:')", "b:contains('de Vídeo:')", "b:contains('Qualidade de Áudio e Vídeo:)"]));
            $this->ano_lancamento = $this->removerEspacosEmBranco(self::pegar_dados_e_verificar($this->dom, ["b:contains('Ano de Lançamento:')", "b:contains('Lançamento:')"]));
            $this->duracao = self::pegar_dados_e_verificar($this->dom, ["b:contains('Duração')"]);
            self::verificar_e_usar_themoviedb($this->theMovieDB, $this);
            self::usar_link_youtube($this);
            self::verificar_e_usar_imdb($this->imdb, $this);
            self::preparar_categorias($this);
            self::preparar_imagens($this);
            self::verificar_se_e_imagem_cinema($this);
        }

        private function removerEspacosEmBranco($texto)
        {
            return FuncoesUteis::multipleReplace(['&nbsp;'], '', $texto);
        }

        private function carregar_dados_serie()
        {
            $titulo_serie = $this->dom->findOne("h1.entry-title")->text();
            $this->serie_temporada = FuncoesUteis::identificar_temporada_serie($titulo_serie);
            $this->qualidade_audio = empty($this->qualidade_audio) ? "10" : $this->qualidade_audio;
            $this->qualidade_video = empty($this->qualidade_video) ? "10" : $this->qualidade_video;
            $this->pegar_texto_serie_anotacao();
            $this->pegar_links_download_serie();
        }

        private function pegar_texto_serie_anotacao()
        {
            $textos = $this->dom->findMultiOrFalse("span[style='color: #008000;']");
            $executou = false;
            $html = "<span style='color:red'>";
//            foreach ($textos as $texto) {
//                dump($texto->text());
//                if (!is_null($texto->nextSibling())) {
//                    dump($texto->nextSibling()->html());
//                    if (count($texto->nextSibling()->find("br")) > 0) {
//                        $html .= $texto->text() . "<br>";
//                        $executou = true;
//                    }
//                }
//            }
            if ($textos != false) {
                $textos_anterior = "";
                foreach ($textos as $texto) {
                    $texto_encontrado = trim($texto->text());
                    if (!Str::contains($texto_encontrado, $textos_anterior)) {
                        $executou = true;
                        $html .= $texto_encontrado . "<br>";
                        $textos_anterior .= $texto_encontrado;
                    }
                }
            }
            $html .= "</span>";
            $this->serie_anotacao = $executou == true ? $html : "";
        }

        private function check_spans(&$spans, array $check_spans, array $tags)
        {
            foreach ($tags as $tag) {
                foreach ($check_spans as $check) {
                    $spans = $this->dom->findMultiOrFalse("$tag:contains('" . $check . "')");
                    if ($spans != false) {
                        break 2;
                    }
                }
            }
        }

        private function pegarSpans()
        {
            $lista_span = [];
            $tags = ["span", "strong", "h2 > strong"];
            $selectors = [":contains('Versão')", ":contains('::')", ":contains('#8212;')"];
            foreach ($tags as $tag) {
                foreach ($selectors as $selector) {
                    $css_seletor = $tag . $selector;
                    $spans = $this->dom->findMultiOrFalse($css_seletor);
                    if ($spans != false) {
                        foreach ($spans as $span) {
                            $lista_span[] = $span->text();
                        }
                    }
                }
            }
            $spans = $this->dom->findMultiOrFalse("h2 > strong");
            if ($spans != false) {
                foreach ($spans as $span) {
                    $lista_span[] = $span->text();
                }
            }
            return $lista_span;
        }

        private function pegar_links_download_serie()
        {
            $lista_span_texto = $this->pegarSpans();
            $dados = [];
            $count_span = 0;
            $links = $this->dom->findMulti("a[href*='magnet']");
            $lista_episodio = "";
            $texto_anterior_div = "";
            $total_span = count($lista_span_texto);
            foreach ($links as $link) {
                try {
                    $texto_anterior = "|";

                    $elemento_texto = $link->previousSibling();
                    while (!Str::contains($texto_anterior, ["Ep", ":",'Temporada Completa'])) {
                        try {
                            $texto_anterior = $elemento_texto->text();
                            $elemento_texto = $elemento_texto->previousSibling();
                        } catch (\Throwable $ex) {
                            $texto_anterior = "Temporada Completa";
                            break;
                        }
                    }
                    if ($link->findOneOrFalse("img") != false) {
                        $qualidade = $this->identificar_qualidade_por_imagem($link->findOne("img")->getAttribute("src"));
                        if ($qualidade == false) {
                            $pai = $link->parentNode();
                            $p_com_texto = "";
                            $anterior = $pai->previousSibling();
                            for($index_p = 0; $index_p < 6; $index_p++){
                                if($anterior != null){
                                    $elemento_anterior_texto = $anterior->text();
                                    if(Str::contains($elemento_anterior_texto, ["Ep", ":",'Temporada Completa'])){
                                        $p_com_texto = $elemento_anterior_texto;
                                        break;
                                    }
                                    $anterior = $anterior->previousSibling();
                                }
                            }
                            $qualidade = $this->identificar_qualidade_por_imagem($p_com_texto);
                            if ($qualidade == false) {
                                $qualidade = $this->identificar_qualidade_por_imagem($texto_anterior);
                               if($qualidade == false){
                                   $qualidade = "MAGNET";
                               }
                            }
                        }
                        $texto_div = $texto_anterior;
                        $texto_link = $qualidade;
                        $texto_para_verificar = $texto_div . $qualidade . $texto_link;
                        if (Str::contains($texto_anterior_div, "Ep")) {
                            if ($total_span > ($count_span + 1)) {
                                $count_span++;
                            }
                        }
                    } else {
                        $texto_div = $this->remover_texto_links($texto_anterior);
                        $texto_link = $link->text();
                        $texto_para_verificar = $texto_div . $texto_link;
                        if (Str::contains($texto_para_verificar, " e")) {
                            $resultado = trim(preg_replace('/e.*/', '', $texto_para_verificar));
                            if (Str::contains($lista_episodio, $resultado)) {
                                $count_span++;
                                $lista_episodio = "";
                            }
                        }
                    }
                    if (Str::contains($lista_episodio, $texto_para_verificar)) {
                        $count_span++;
                        $lista_episodio = "";
                    } elseif (Str::contains($texto_anterior_div, "Temporada") && !Str::contains($texto_para_verificar,
                            "Temporada")) {
                        $count_span++;
                        $lista_episodio = "";
                    } elseif (Str::contains($texto_para_verificar,
                            "ao") && !Str::contains($lista_episodio, "ao")) {
                        $remove_ao = trim(preg_replace('/ao.*/', '', $texto_para_verificar));
                        if (Str::contains($lista_episodio, $remove_ao)) {
                            $count_span++;
                            $lista_episodio = "";
                        }
                    } elseif ($texto_anterior_div !== $texto_div) {
                        if (Str::contains($lista_episodio, $texto_div)) {
                            $count_span++;
                            $lista_episodio = "";
                        }
                    }
                    $lista_episodio .= " " . $texto_para_verificar;
                    if ($total_span >= $count_span) {
                        $dados[$this->remover_caracteres($lista_span_texto[$count_span])][$texto_div][$texto_link] = $link->getAttribute("href");
                        $texto_anterior_div = $texto_div;
                    }
                } catch (\Throwable $ex) {
                    
                }
            }
            $this->links_download_serie = $dados;
        }

        private function remover_caracteres($texto)
        {
            return trim(FuncoesUteis::multipleReplace(["::", "&#8212;", "—"], "", $texto));
        }

        private function pegar_links_download_serie_antigo()
        {
            $spans = $this->dom->find("span:contains('Versão')");
            $dados = [];
            foreach ($spans as $span) {
                $div = $span->parentNode()->parentNode();
                for ($i = 0; $i < 2000; $i++) {
                    if (!is_null($div)) {
                        if (count($div->find("hr")) > 0) {
                            break;
                        }
                        $links = $div->find("a[href*='magnet']");
                        if (count($links) > 0) {
                            foreach ($links as $link) {
                                if (count($link->find("img")) > 0) {
                                    $dados[$span->text()]["Temporada Completa"][] = [$this->identificar_qualidade_por_imagem($link->findOne("img")->getAttribute("src")) => $link->getAttribute("href")];
                                } else {
                                    $dados[$span->text()][$this->remover_texto_links($div->text())][] = [$link->text() => $link->getAttribute("href")];
                                }

                            }
                        }
                        $div = $div->nextSibling();
                    } else {
                        break;
                    }
                }
            }
            $this->links_download_serie = $dados;
        }

        private function identificar_qualidade_por_imagem($link_img)
        {
            if (Str::contains($link_img, "1080")) {
                return "1080p";
            } elseif (Str::contains($link_img, "720")) {
                return "720p";
            } elseif (Str::contains($link_img, "480")) {
                return "480p";
            } else if (Str::contains($link_img, "x264")) {
                return "720p x264";
            }
            return false;
        }

        private function remover_texto_links($texto)
        {
            preg_match('/(.*):/', $texto, $resultado);
            return isset($resultado[1]) ? $resultado[1] : $texto;
        }

        private function pegar_generos()
        {
            try {
                return $this->removerEspacosEmBranco(self::pegar_dados_e_verificar($this->dom, ["b:contains(Gênero)"]));
            } catch (\Exception $ex) {
                \Log::error($ex);
                return "";
            }
        }
    }
