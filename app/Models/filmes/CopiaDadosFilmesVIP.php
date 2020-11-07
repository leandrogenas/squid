<?php


    namespace App\Models\filmes;


    use App\Models\filmes\Utils\PostFilmesVIP;
    use App\Models\IMDB;
    use voku\helper\HtmlDomParser;

    class CopiaDadosFilmesVIP
    {
        public $log = "";

        /**
         * @param PostFilmesVIP[] $posts
         */
        public function prepararPostagem($posts)
        {
            foreach ($posts as $post) {
                $content = $post->post_content;
                $dom = HtmlDomParser::str_get_html($content);
                $imdb = $this->pegarIMDB($dom,$post);
                if($imdb != false){
                    if(!$this->verificar_se_existe_postagem($imdb->titulo)){
                        
                    }
                }
            }
        }

        private function pegarIMDB(HtmlDomParser $dom,$post){
            try{
                $link = $dom->findOneOrFalse("a[href*='imdb']");
                if ($link != false) {
                    $link_imdb = $link->getAttribute("href");
                    $re = '/title\/(.*)\/|title\/(.*)/m';
                    preg_match_all($re,$link_imdb,$matches);
                    $imdb_id = empty($matches[1][0]) ? $matches[2][0]:$matches[1][0];
                    $imdb = new IMDB($imdb_id);
                    $imdb->pegar_dados_filme();
                    return $imdb;
                }else{
                    $this->log .= " Não foi possível pegar o IMDB no post: ".$post->ID."\n";
                    return false;
                }
            }catch (\Throwable $ex){
                \Log::error($ex);
                return false;
            }
        }

        private function verificar_se_existe_postagem($nome)
        {
            $url = "https://filmesviatorrents.biz/?s=" . urlencode($nome);
            $dom = HtmlDomParser::file_get_html($url);
            $resultado = $dom->findOneOrFalse('li.TPostMv');
            if($resultado == false){
                return false;
            }else{
                return true;
            }
        }
    }
