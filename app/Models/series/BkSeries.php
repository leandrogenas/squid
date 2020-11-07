<?php


    namespace App\Models\series;

    use App\Models\filmes\TheMovieDB;
    use App\Utils\FuncoesUteis;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use Stichoza\GoogleTranslate\GoogleTranslate;
    use voku\helper\HtmlDomParser;

    /**
     * Class BkSeries
     * @property string $link
     * @property HtmlDomParser $dom
     * @property TheMovieDB $theMovieDB
     * @package App\Models\series
     */
    class BkSeries extends Serie
    {
        private $link, $dom;
        private $dadosJson;

        /**
         * BkSeries constructor.
         * @param string $link
         */
        public function __construct(string $link)
        {
            $this->link = $link;
            $this->dadosJson = [];
        }

        private function carregar_site()
        {
            $this->dom = HtmlDomParser::file_get_html($this->link);
            $this->carregar_categorias();
        }

        private function carregar_categorias(){
            $categorias = $this->dom->findOneOrFalse("span.categoria-video");
            if($categorias != false){
                $texto = $categorias->text();
                $texto = FuncoesUteis::multipleReplace(['Categorias:',':'],'',$texto);
                $this->categorias_separadas = explode(",",$texto);
            }
        }

        /**
         * @param $link_campanha
         * @return array|bool
         */
        private function getCodeVideo($link_campanha)
        {
            if (Str::contains($link_campanha, "v=")) {
                return $this->prepara_link_com_v($link_campanha);
            }elseif (Str::contains($link_campanha, "w=")){
                return $this->preparap_link_com_w($link_campanha);
            } elseif(Str::contains($link_campanha, "t=")) {
                return $this->prepara_link_com_t($link_campanha);
            }elseif(Str::contains($link_campanha, "l=")){
                $link_letsup = "https://letsupload.co/plugins/mediaplayer/site/_embed.php?u=" . explode("l=", $link_campanha)[1];
                return ["id"=>$link_letsup,"link"=>$link_letsup];
            }else{
               return false;
            }
        }


        public function carregar_dados()
        {
            $this->carregar_site();
            self::verificar_e_usar_themoviedb($this->theMovieDB, $this);;
            $this->pegar_audio();
//        self::preparar_imagens($this);
//        $this->pegar_links_download_total();
        }

        public function pegar_audio()
        {
            $this->audio = str_replace("Áudio:", "", $this->dom->findOne("span.audio-video")->text());
        }




//    private function pegar_links_download_total()
//    {
//        $div_tab_content = $this->dom->findMulti("div.tab_content");
//        $links_download = [];
//        $temporada = 1;
//        foreach ($div_tab_content as $div_tab) {
//            $div_tercos = $div_tab->findMulti("div.um_terco");
//            foreach ($div_tercos as $div_terco) {
//                $ul = $div_terco->findOne("div > ul");
//                $tipo = $ul->findOne("p")->text();
//                $li_s = $ul->findMulti("li");
//                foreach ($li_s as $li) {
//                    $a = $li->findOne("a");
//                    $texto_link = $a->text();
//                    $link = $a->getAttribute("href");
//                    if (!empty($link)) {
//                        try{
//                            $link = $this->getCodeVideo($link);
//                            $links_download[$temporada][$tipo][] = ["texto" => $texto_link, "link" => $link];
//                        }catch (\Throwable $ex){
//                            \Log::info("Erro na serie: ".$this->titulo." no ".$texto_link);
//                        }
//                    }
//                }
//            }
//            $temporada++;
//        }
//        $this->links_download = $links_download;
//    }
        public function pegar_links_para_episodio()
        {
            $config = $this->configLinkDownload;
            $div_tab_content = $this->dom->findMulti("div.tab_content");
            $temporadas = [];
            $temporada = 1;
            foreach ($div_tab_content as $div_tab) {
                $div_tercos = $div_tab->findMulti("div.um_terco");
                if ($config->pegar_tudo) {
                    $this->preparar_links_para_download($temporadas, $div_tercos, $config, $temporada);
                } elseif (array_key_exists($temporada, $config->pegar_temporada_e_episodios)) {
                    $this->preparar_links_para_download($temporadas, $div_tercos, $config, $temporada);
                }
                $temporada++;
            }
            $this->temporadas = $temporadas;
        }

        private function getOnlyEpisodioNumber($texto)
        {

            return trim( preg_replace('/[^0-9]/', '', $texto));
        }

        /**
         * @param Temporadas $temporada
         * @param $temporada_numero
         * @param string $link
         * @param string $tipo
         * @param string $texto_link
         */
        private function preparar_episodio_links(Temporadas $temporada, $temporada_numero, string $link, string $tipo, string $texto_link)
        {
            $result = $this->getCodeVideo($link);
            if($result !== false){
                $ep = new Episodio();
                $ep->titulo = $this->titulo;
                $ep->temporada = $temporada_numero;
                $ep->tipo = $tipo;
                $ep->episodio = $texto_link;
                $ep->link = $result["id"];
                $ep->link_original = $result["link"];
                $ep->temporada_class = $temporada;
                $ep->prepara_informacao($this->theMovieDB);
                $temporada->episodios[] = $ep;
                $this->dadosJson[] = ["nome" => $this->titulo, "link" => $result["link"], "episodio" => $texto_link, "temporada" => $temporada_numero, "audio" => $tipo];
            }
        }

        /**
         * @param array $temporadas
         * @param $div_tercos
         * @param ConfigLinksDownload $config
         * @param int $temporada_numero
         */
        private function preparar_links_para_download(&$temporadas, $div_tercos, ConfigLinksDownload $config, int $temporada_numero): void
        {
            $t = new Temporadas($this);
            $t->temporada_numero = $temporada_numero;
            $t->titulo = $this->titulo;
            $t->preparar_informacoes($this->theMovieDB);
            foreach ($div_tercos as $div_terco) {
                $ul = $div_terco->findOne("div > ul");
                $tipo = $ul->findOne("p")->text();
                if(empty($tipo)){
                    $tipo = $ul->findOne("strong")->text();
                }
                $li_s = $ul->findMulti("li");
                foreach ($li_s as $li) {
                    $a = $li->findOne("a");
                    $episodio_numero = $this->getOnlyEpisodioNumber($a->text());
                    $link = $a->getAttribute("href");
                    if (!empty($link)) {
                        try {
                            if ($config->pegar_tudo) {
                                $this->preparar_episodio_links($t, $temporada_numero, $link, $tipo, $episodio_numero);
                            } else {
                                if (in_array($tipo, $config->pegar_temporada_e_episodios[$temporada_numero]["tipos"])) {
                                    $ep_start = $config->pegar_temporada_e_episodios[$temporada_numero]["episodio_start"];
                                    $ep_end = $config->pegar_temporada_e_episodios[$temporada_numero]["episodio_end"];
                                    if ($episodio_numero >= $ep_start & $episodio_numero <= $ep_end) {
                                        $this->preparar_episodio_links($t, $temporada_numero, $link, $tipo, $episodio_numero);
                                    }
                                }
                            }
                        } catch (\Throwable $ex) {
                            \Log::info("Erro na serie: " . $this->titulo . " no episódio " . $episodio_numero . " na temporada ".$temporada_numero." tipo: " . $tipo);
                            \Log::error($ex);
                        }
                    }
                }
            }
            $temporadas[] = $t;
            $this->salvar_dados_json();
        }

        private function salvar_dados_json()
        {
            $dados_json = json_encode($this->dadosJson);
            $dado = $dados_json . "===================";
            Storage::append("lista_episodios.txt", $dado);
            \Session::put("json", $dados_json);
            \Session::save();
        }

        /**
         * @param $link_campanha
         * @return array
         */
        private function prepara_link_com_v($link_campanha): array
        {
            $delimiter = "v=";
            $link_preparado = "https://www.bkseries.com/videozin/video-play.mp4/?contentId=" . explode($delimiter, $link_campanha)[1];
//                $options = array(
//                    CURLOPT_RETURNTRANSFER => true,   // return web page
//                    CURLOPT_HEADER => true,  // don't return headers
//                    CURLOPT_FOLLOWLOCATION => true,   // follow redirects
//                    CURLOPT_MAXREDIRS => 10,     // stop after 10 redirects
//                    CURLOPT_ENCODING => "",     // handle compressed
//                    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'], // name of client
//                    CURLOPT_AUTOREFERER => true,   // set referrer on redirect
//                    CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
//                    CURLOPT_TIMEOUT => 120,    // time-out on response
//                    CURLOPT_NOBODY => true
//                );
//
//                $ch = curl_init($link_preparado);
//                curl_setopt_array($ch, $options);
//                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: __cfduid=d12b6ff746a47afb701d20a2fe63113b11584984729; SERVERID68970=264081; cdn_12=1; PHPSESSID=c866891bfb35dea15f1293f1bacefd4a; _ga=GA1.2.1014448095.1584984731; _gid=GA1.2.1219776068.1584984731; packet2=TlNObmJQNVF4R3V2azQ5WVRZZ0ZVTFZLT2E3NjM4OFRiZE9qb0oxcVhyamFsVXJHQ0VRczNzT3JSTmhtWnVtQQ%3D%3D"));
//
//                $content = curl_exec($ch);
//
//                curl_close($ch);
//                $data = explode("\n", $content);
//                $result = array_filter($data, function ($var) {
//                    return preg_match('/Location: (.*)/m', $var);
//                });
//                preg_match_all('/&id=(.*?)&/m', reset($result), $matches, PREG_SET_ORDER, 0);
            $headers = get_headers($link_preparado);
            $final_url = "";
            foreach ($headers as $h) {
                if (substr($h, 0, 10) == 'Location: ') {
                    $final_url = trim(substr($h, 10));
                    break;
                }
            }
            preg_match_all('/&id=(.*?)&/m', $final_url, $matches, PREG_SET_ORDER, 0);
            $link_id = $matches[0][1];
            return ["id" => $link_id, "link" => $final_url];
        }

        /**
         * @param $link_campanha
         * @return array
         */
        private function preparap_link_com_w($link_campanha): array
        {
            $link_wix = "https://www.bkseries.com/video/player/wix.php?w=" . explode("w=", $link_campanha)[1];
            $link_pronto = HtmlDomParser::file_get_html($link_wix)->findOne("iframe")->getAttribute("src");
            return ["id" => $link_pronto, "link" => $link_pronto];
        }

        /**
         * @param $link_campanha
         * @return array
         */
        private function prepara_link_com_t($link_campanha): array
        {
            $link_bloger = "https://www.blogger.com/video.g?token=" . explode("t=", $link_campanha)[1];
            $doc = HtmlDomParser::file_get_html($link_bloger);
            $script = $doc->findOne("script")->html();
            $re = '/&id=(.*?)&/m';
            preg_match_all($re, $script, $matches, PREG_SET_ORDER, 0);
            $link_id = $matches[0][1];
            $re = '/play_url":"(.*)",/m';
            preg_match_all($re, $script, $matches, PREG_SET_ORDER, 0);
            $link_normal = $matches[0][1];
            return ["id" => $link_id, "link" => $link_normal];
        }
    }
