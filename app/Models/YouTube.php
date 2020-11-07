<?php


    namespace App\Models;


    use App\Enums\AppDadosEnum;
    use App\Utils\FuncoesUteis;
    use Illuminate\Support\Str;
    use voku\helper\HtmlDomParser;

    class YouTube
    {
        public static function pesquisar_trailer_filme_embed($nome)
        {
            try {
                $videoList = \Alaouy\Youtube\Facades\Youtube::searchVideos($nome . ' trailer');
                $links_prontos = [];
                foreach ($videoList as $video) {
                    $l_id = $video->id->videoId;
                    $links_prontos[] = ["embed" => "https://www.youtube.com/embed/" . $l_id, "id" => $l_id];
                }
                return $links_prontos;
            } catch (\Exception $ex) {
                \Log::error($ex);
                return [];
            }

        }

        public static function pesquisar_trailer_serie($nome)
        {
            try {
                $videoList = \Alaouy\Youtube\Facades\Youtube::searchVideos($nome . ' trailer');
                $links_prontos = [];
                foreach ($videoList as $video) {
                    $l_id = $video->id->videoId;
                    $links_prontos[] = "https://www.youtube.com/embed/" . $l_id;
                }
                return $links_prontos;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return [];
            }
        }

        public static function pesquisar_jogo($nome_jogo)
        {
            try {
                $videoList = \Alaouy\Youtube\Facades\Youtube::searchVideos($nome_jogo . ' gameplay');
                $links_prontos = [];
                foreach ($videoList as $video) {
                    $l_id = $video->id->videoId;
                    $links_prontos[] = "https://www.youtube.com/embed/" . $l_id;
                }
                return $links_prontos;
            } catch (\Exception $ex) {
                \Log::error($ex);
                return [];
            }
        }
    }
