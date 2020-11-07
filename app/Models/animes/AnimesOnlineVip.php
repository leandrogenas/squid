<?php


    namespace App\Models\animes;


    use voku\helper\HtmlDomParser;

    class AnimesOnlineVip
    {
        public static function procurar_anime($nome){
            $lista = [];
            try{
                $dom = HtmlDomParser::file_get_html("https://animesonline.vip/?s=".urlencode($nome));
                $encontrados = $dom->findMultiOrFalse("div.video-thumb > a[itemprop='URL']");
                if($encontrados != false){
                    $count = count($encontrados);
                    $limite = $count >= 3 ? 3:$count;
                    for ($i = 0; $i < $limite; $i++){
                        $link = $encontrados[$i]->getAttribute("href");
                        $pagina = HtmlDomParser::file_get_html($link);
                        $shorlink = $pagina->findOneOrFalse("link[rel='shortlink']");
                        if($shorlink != false){
                            $href = $shorlink->getAttribute("href");
                            $id = explode("p=",$href)[1];
                            $lista["lista"][] = ["id"=>$id,"link"=>$link];
                        }
                    }
                }
            }catch (\Throwable $ex){
                \Log::error($ex);
            }
            return $lista;
        }
    }
