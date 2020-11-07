<?php


    namespace App\Models\filmes\Utils;


    use Illuminate\Support\Facades\Storage;
    use voku\helper\HtmlDomParser;

    class BuscaImagemOffline
    {
        private $url_site, $page_start, $page_end;
        private $posts_ids = [];

        /**
         * BuscaImagemOffline constructor.
         * @param string $url_site
         * @param int $page_start
         * @param int $page_end
         */
        public function __construct($page_start = 2, $page_end = 1000, $url_site = "https://filmestorrent.vip/")
        {
            $this->url_site = $url_site;
            $this->page_start = $page_start;
            $this->page_end = $page_end;
        }

        public function start()
        {
            try{
                for ($i = $this->page_start; $i <= $this->page_end; $i++) {
                    $dom = $this->getSiteOrFail($i);
                    if ($dom != false) {
                        $div_corpos = $dom->findMultiOrFalse("div.postContainerCorpo");
                        if ($div_corpos != false) {
                            foreach ($div_corpos as $div_corpo) {
                                $img = $div_corpo->findOneOrFalse("img");
                                if ($img != false) {
                                    if (!$this->verificar_imagem($img->getAttribute("src"))) {
                                        $link = $div_corpo->findOneOrFalse("a[href*='filmestorrent']");
                                        if ($link != false) {
                                            $post_id = $this->pegar_id_postagem($link->getAttribute("href"));
                                            if ($post_id != false) {
                                                $this->posts_ids[] = $post_id;
                                            }
                                        } else {
                                            \Log::info("Não foi retornado o link. IMG: " . $img->getAttribute("src"));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }catch (\Throwable $ex){
                \Log::error($ex);
            }
            $text = implode(",", $this->posts_ids);
            $this->save_in_text($text);
            return $text;
        }

        private function save_in_text($text)
        {
            try {
                Storage::append("lista_imagens_offline.txt", $text.",");
            } catch (\Throwable $ex) {
                \Log::error($ex);
            }
        }

        private function pegar_id_postagem($url)
        {
            try {
                $dom = HtmlDomParser::file_get_html($url);
                $shor_link = $dom->findOneOrFalse("link[rel='shortlink']");
                if ($shor_link != false) {
                    $href = $shor_link->getAttribute("href");
                    return explode("p=", $href)[1];
                } else {
                    \Log::info("Não foi possivel pegar ID da postagem em: " . $url);
                    return false;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                \Log::info("Não foi possível pegar o ID da postagem em: " . $url);
                return false;
            }
        }

        private function verificar_imagem($url)
        {
            try {
                HtmlDomParser::file_get_html($url);
                return true;
            } catch (\Throwable $ex) {
                return false;
            }
        }

        /**
         * @param $page
         * @return bool|HtmlDomParser
         */
        private function getSiteOrFail($page)
        {
            try {
                return HtmlDomParser::file_get_html($this->url_site . "page/" . $page);
            } catch (\Throwable $ex) {
                \Log::info("parou na page: " . $page);
                \Log::error($ex);
                return false;
            }
        }
    }
