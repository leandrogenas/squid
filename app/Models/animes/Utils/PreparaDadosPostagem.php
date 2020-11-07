<?php


    namespace App\Models\animes\Utils;


    use App\Models\animes\Animes;
    use App\Models\animes\Episodio;

    class PreparaDadosPostagem
    {
        public static function preparaDadosEpisodio(Episodio &$episodio, $imagem_id = null)
        {
            if (!is_null($imagem_id)) {
                $content = ["post_thumbnail" => (int)$imagem_id, "comment_status" => "closed"];
            }
            $content["custom_fields"] = [
                ["key" => "episodio_anime", "value" => $episodio->episodio_anime],
                ["key" => "episodio_capa", "value" => $episodio->episodio_capa],
                ["key" => "episodio_ep", "value" => $episodio->episodio_numero],
                ["key" => "episodio_letra", "value" => $episodio->episodio_letra],
                ["key" => "episodio_mp4", "value" => $episodio->episodio_mp4],
                ["key" => "episodio_tempo", "value" => $episodio->episodio_tempo],
                ["key" => "episodio_tipo", "value" => $episodio->episodio_tipo],
                ["key" => "episodio_player_2", "value" => $episodio->link_player_2]
            ];
            $content["post_type"] = "episodio";
            $episodio->content = $content;
            return $content;
        }

        public static function prepararDadosAnime(Animes &$anime, $imagem_id = null)
        {
            if (!is_null($imagem_id)) {
                $content = [
                    "terms_names" => ["category" => $anime->getGeneros()],
                    "post_thumbnail" => (int)$imagem_id, "comment_status" => "open"
                ];
            } else {
                $content = ["terms_names" => ["category" => $anime->getGeneros()], "comment_status" => "open"];
            }
            $content["custom_fields"] = [
                ["key" => "anime_data", "value" => $anime->data],
                ["key" => "anime_letra", "value" => $anime->anime_letra],
                ["key" => "anime_tipo", "value" => $anime->tipo],
                ["key" => "anime_titulo", "value" => $anime->titulo_original]
            ];
            $content["post_type"] = "post";
            $anime->content = $content;
            return $content;
        }
    }
