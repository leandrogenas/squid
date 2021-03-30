<?php


    namespace App\Models\animes;


    use App\Enums\AnimeTipo;
    use App\Utils\FuncoesUteis;
    use Illuminate\Support\Str;
    use Ixudra\Curl\Facades\Curl;
    use voku\helper\HtmlDomParser;

    class AnimesVision extends Animes
    {

        public static function pegar_titulo_anime($link)
        {
            $dom = HtmlDomParser::file_get_html($link);
            $result = $dom->findOneOrFalse("div.dc-info > p.alias");
            if ($result != false) {
                $encontrado = ["original" => $result->text(), "normal" => FuncoesUteis::remover_palavras_animes($dom->findOne("ol.breadcrumb > li.active")->text())];
            } else {
                $normal = FuncoesUteis::remover_palavras_animes($dom->findOne("ol.breadcrumb > li.active")->text());
                $encontrado = ["original" => $normal, "normal" => $normal];
            }
            $episodios = $dom->findMultiOrFalse("li.ep-item");
            if($episodios != false){
                $encontrado["count_episodios"] = count($episodios);
            }
//            \Log::info(print_r($encontrado,true));
            return $encontrado;
        }

        public static function verificar_url_correta($url){
            try{
                $dom = HtmlDomParser::file_get_html($url);
                $link = $dom->findOneOrFalse("a#listEp");
                if($link != false){
                    return $link->getAttribute("href");
                }
            }catch (\Throwable $ex){
                \Log::error($ex);
            }
            return explode("/episo", $url)[0];
        }

        private function prepara_descricao($dom,$titulo_anime){
            $descricao = "<h2>Assistir Todos os Episódios de $titulo_anime</h2>".$this->pegarValor($dom, "div.dci-desc", "Assistir " . $titulo_anime);;
            return $descricao;
        }

        public function carregar($episodio_start = 1, $episodio_end = 2)
        {
            try {
                $dom = HtmlDomParser::file_get_html($this->link);
                $this->tipo = $this->pegarValores($dom, "b:contains(Idioma)", "Legendado");
                $titulo_anime = FuncoesUteis::remover_palavras_animes(empty($this->titulo) ? $dom->findOne("ol.breadcrumb > li.active")->text():$this->titulo);
//                $duracao = $this->pegar_duracao($dom)
                $titulo_original = FuncoesUteis::remover_palavras_animes($this->pegarValor($dom, "div.dc-info > p.alias", $titulo_anime));
                $this->generos = $this->pegarValores($dom, "b:contains(Gêneros)", "");
                $this->link_capa = $dom->findOne("div.dc-thumb > img")->getAttribute("src");
                $this->descricao = $this->prepara_descricao($dom,$titulo_anime);
                $this->titulo = $this->preparatitulo($titulo_anime);
                $this->titulo_original = $titulo_original;
                $lista_episodios = $dom->findMultiOrFalse("li.ep-item");
                $episodio_letra = strtolower($titulo_anime[0]);
                $this->anime_letra = $episodio_letra;
                if ($lista_episodios != false) {
                    if ($episodio_end <= count($lista_episodios)) {
                        for ($i = $episodio_start; $i <= $episodio_end; $i++) {
                            $lista = $lista_episodios[($i - 1)];
                            $link_ep = $lista->findOneOrFalse("div.sli-btn > a:nth-child(2)");
                            if ($link_ep != false) {
                                $link_download_ep = $link_ep->getAttribute("onclick");
                                $title = $link_ep->getAttribute("title");
                                $re = '/open\(\'(.*?)\',/m';
                                preg_match_all($re, $link_download_ep, $matches, PREG_SET_ORDER, 0);
                                $link_download_ep = $matches[0][1];
                                $this->pegar_dados_episodio($link_download_ep, $titulo_anime, $i, $this->tipo, $episodio_letra, $title);
                            }
                        }
                    } else {
                        $this->log .= "A quantidade de episódio pedido é maior do que a disponível em: " . $this->link . " \n";
                    }
                } else {
                    $this->log .= "Não foi encontrado episodios em: " . $this->link . " \n";
                    return false;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve um erro: " . $ex->getMessage() . " \n";
                return false;
            }
        }

        private function pegar_duracao($dom)
        {
            try {
                $duracao = trim($dom->findOne("b:contains(Duração)")->nextSibling()->text());
                preg_match_all('!\d+!', $duracao, $matches);
                if (isset($matches[0][0])) {
                    if (isset($matches[0][1])) {
                        return $matches[0][0] . ":" . $matches[0][1] . ":00";
                    }
                    return $matches[0][0] . ":00";
                } else {
                    return "24:00";
                }
            } catch (\Throwable $ex) {
                return "24:00";
            }
        }

        private function pegarValor(HtmlDomParser $dom, $selector, $default_value = "")
        {
            try {
                $result = $dom->findOneOrFalse($selector);
                if ($result != false) {
                    return empty($result) ? $default_value : $result->text();
                } else {
                    return $default_value;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                \Log::info("pegarvalor: ".$selector);
                return $default_value;
            }
        }

        private function pegarValores($dom, $selector, $default_value = "")
        {
            try {
                $result = trim($dom->findOne($selector)->nextSibling()->text());
                return empty($result) ? $default_value : $result;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                \Log::info("pegarvalores: ".$selector);
                return $default_value;
            }
        }

        private function pegar_dados_episodio($link_episodio, $anime, $episodio_numero, $tipo, $episodio_letra, $title_episodio)
        {
            try {
                $response = Curl::to($link_episodio)
                    ->withHeader("Cookie: __cfduid=d895d8512a0c46a3f2230b2e8e00260171614740569; XSRF-TOKEN=eyJpdiI6IkxUUnpSU1g3bjg2WmdkSTZoc0RmQ2c9PSIsInZhbHVlIjoiYkZjdzZWa0hLQXhTWW96WDA3S0ovWXJyZFplYWErd3lXbzZKTmVEY3djLzA3RVg0TGhLbE03elJrV1Z5VllnbHpjRUc3M0N0WUJrbENYR1B5c2NnZjRST1lpUzVaZmdZelp5bFlWVTdUUWNLRTJ4RWdvQXgyNG1ud2dxbUxTZUkiLCJtYWMiOiIwZTUwYWU2NWJlMjc5YWJiNDRiNzVjYjA5MGVlMTlmM2MyYWUwOWY0ZDA0MGQ3YWRlYTU3Mjg2YjNkNGYyMTBlIn0%3D; animesvision_session=eyJpdiI6IjRoUXJiV3dCMWxxQWIvUkRIT1NVa3c9PSIsInZhbHVlIjoiUVphY1VhcDlBcVhhYlVIcWpkS29IVnlUTlZLZURHakszT3d5VEdwUGk5djRZVU11ZCt5M2lKL0liZWFGMjlEUEs5RXRFRDU1RXZwU1NESktOZEl1MnRRUjRrQUZoQTJmZE56T1ZYZlYyYUlrQXI0THpiZ3Y2RUhQQ2lCeUJMYW0iLCJtYWMiOiI5MzVjNWZmZWRkY2Q4NjY1ODE1ZjM1NjY0NzIyOTQzYmJmODU3YjRlNDFiZGJlODExYThjZDRkNDk0NDU0YTVjIn0%3D")->withHeader("Accept-Language: pt-BR")->get();
                $dom = HtmlDomParser::str_get_html($response);
                $link_download = $dom->findOneOrFalse("a.btn.btn-danger.btn-lg:contains('VisionVIP SD')");
                $episodio_numero = $this->preparar_numero_episodio($episodio_numero, $title_episodio);
                if ($this->tipo_anime == AnimeTipo::EPISODIO) {
                    $tipo_anime = " Episódio " . $episodio_numero;
                } else {
                    $tipo_anime = $this->verificar_titulo(strtolower($anime)) ? "" : " " . $this->tipo_anime . " " . $episodio_numero;
                }
                if ($link_download != false) {
                    $ep = new Episodio();
                    $ep->link_download = $link_download->getAttribute("href");
                    $ep->episodio_letra = $episodio_letra;
                    $ep->episodio_tipo = $tipo;
                    $ep->episodio_numero = $episodio_numero;
                    $ep->titulo = $anime . " -" . $tipo_anime;
                    $ep->descricao = $this->tipo_anime == AnimeTipo::FILME ? $this->descricao : "Assistir " . $anime . " -" . $tipo_anime;
                    $ep->nome_anime = $anime;
                    $ep->cliente = $this->cliente;
                    $ep->titulo_anime = $this->titulo;
                    $this->episodios[] = $ep;
                } else {
                    $this->log .= "Não foi encontrado o link de download em: " . $link_episodio . " \n";
                    return false;
                }
                return true;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Não foi possivel pegar dados do episódio em: " . $link_episodio . " \n";
                return false;
            }
        }
        private function ajustaLinkDownload($link){
            return preg_replace('/down(.)/m','down6',$link);
        }

        private function preparar_numero_episodio($episodio_numero, $texto)
        {
            try {
                $t= preg_replace("/(\[.*\])/m","",$texto);
                preg_match("/Episodio(.*)/m", $t ,$m);
                $re = '!\d+!';
                preg_match_all($re, $m[1], $matches);
                $episodios = [];
                foreach ($matches[0] as $number) {
                    $ep_number = ltrim($number, '0');
                    $episodios[] =  empty($ep_number) ? "0":$ep_number;
                }
                return implode(" & ", $episodios);
            } catch (\Throwable $ex) {
                $this->log .= "Houve um erro ao pegar o número do episódio! Em: " . $texto . ", Foi utilizado o número de posição no lugar! Erro: " . $ex->getMessage() . " \n";
                return $episodio_numero;
            }
        }

    }
