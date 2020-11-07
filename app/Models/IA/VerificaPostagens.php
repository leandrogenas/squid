<?php


    namespace App\Models\IA;

    use App\Enums\PostagemStatus;
    use App\Enums\TipoPostagem;
    use App\Utils\FuncoesUteis;
    use Illuminate\Support\Str;
    use voku\helper\HtmlDomParser;

    class VerificaPostagens
    {
        public function verificar_filmes()
        {
            $listaPostagem = [];
            try {
                $post = Postagem::whereTipo(TipoPostagem::FILME)->whereStatus(PostagemStatus::PUBLICADO)->latest()->first(["title"]);
                $last_title = $post->title;
                $url = "https://torrentdosfilmes.top/";
                for ($page = 1; $page <= 4; $page++) {
                    if ($page == 1) {
                        $dom = HtmlDomParser::file_get_html($url);
                    } else {
                        $dom = HtmlDomParser::file_get_html($url . "page/" . $page);
                    }
                    $links = $dom->findMultiOrFalse("div.title > a");
                    if ($links != false) {
                        foreach ($links as $link) {
                            $text = $link->text();
                            if ($text !== $last_title) {
                                if(!Str::contains($text,"Temporada")){
                                    $listaPostagem[] = ["link"=> $link->getAttribute("href"),"text"=>$text];
                                }
                            } else {
                                break 2;
                            }
                        }
                    }
                }
                return $listaPostagem;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return $listaPostagem;
            }
        }
    }
