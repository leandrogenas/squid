<?php


namespace App\Models\series\Utils;


use App\Models\series\Episodio;
use App\Models\series\Serie;
use App\Models\series\Temporadas;
use App\Models\YouTube;
use App\Utils\FuncoesUteis;
use Jenssegers\Date\Date;

class GerarContentSerie
{
    /**
     * @var Serie $serie
     */
    private $serie;

    /**
     * GerarContentSerie constructor.
     * @param Serie $serie
     */
    public function __construct(Serie &$serie)
    {
        $this->serie = $serie;
    }

    public function gerarContent($imagem_destaque_id = null)
    {
        if (is_null($imagem_destaque_id)) {
            $content = ["terms_names" => ["post_tag" => $this->serie->categorias_separadas, 'category' => [$this->serie->titulo]], "comment_status" => "open"];
        } else {
            $content = [
                "terms_names" => ["post_tag" => $this->serie->categorias_separadas, 'category' => [$this->serie->titulo]],
                "post_thumbnail" => (int)$imagem_destaque_id, "comment_status" => "open"
            ];
        }
        $themovie = $this->serie->theMovieDB;
        $content["custom_fields"] = [
            ["key" => "trailer_youtube", "value" => "<iframe width='560' height='315' src='" . YouTube::pesquisar_trailer_serie($this->serie->titulo) . "' frameborder='0' gesture='media' allow='encrypted-media' allowfullscreen></iframe>"],
            ["key" => "audio", "value" => "DUBLADO & LEGENDADO"],
            ["key" => "nome", "value" => $this->serie->titulo],
            ["key" => "quantidadeepidodios", "value" => $themovie->numero_de_episodios],
            ["key" => "time", "value" => $themovie->episodio_duracao . " min"],
            ["key" => "tipo", "value" => implode(",", $themovie->getGenerosSeparados())],
            ["key" => "data_de_lancamento_", "value" => date("d/m/Y", strtotime($themovie->data_lancamento))]
        ];
        return $content;
    }

    public function gerarContentTemporada(Temporadas $temporadas, $imagem_destaque_id = null)
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        if (is_null($imagem_destaque_id)) {
            $content = ["terms_names" => ["post_tag" => $this->serie->categorias_separadas, 'category' => [$this->serie->titulo]], "comment_status" => "open"];
        } else {
            $content = [
                "terms_names" => ["post_tag" => $this->serie->categorias_separadas, 'category' => [$this->serie->titulo]],
                "post_thumbnail" => (int)$imagem_destaque_id, "comment_status" => "open"
            ];
        }
        $themovie = $this->serie->theMovieDB;
        $date = new Date($temporadas->data_lancamento,'Europe/Brussels');
        $content["custom_fields"] = [
            ["key" => "trailer_youtube", "value" => "<iframe width='560' height='315' src='" . YouTube::pesquisar_trailer_serie($this->serie->titulo . " temporada " . $temporadas->temporada_numero)[0] . "' frameborder='0' gesture='media' allow='encrypted-media' allowfullscreen></iframe>"],
            ["key" => "audio", "value" => $this->serie->audio],
            ["key" => "nome", "value" => $this->serie->titulo],
            ["key" => "quantidadeepidodios", "value" => $themovie->numero_de_episodios],
            ["key" => "time", "value" => $themovie->episodio_duracao . " min"],
            ["key" => "tipo", "value" => implode(",", $themovie->getGenerosSeparados())],
            ["key" => "data_de_lancamento_", "value" => $date->format('j F Y')],
            ["key" => "status", "value" => $themovie->nota],
            ["key" => "temporada", "value" => $temporadas->temporada_numero . "ª Temporada"]
        ];
        return $content;
    }

    public function gerarContentEpisodio(Episodio $episodio, Temporadas $temporada, $imagem_destaque_id = null, &$dados_episodios = [],$index_position = 1)
    {
        if (is_null($imagem_destaque_id)) {
            $content = ["terms_names" => ["category" => $episodio->getCategoriaEpisodio()], "comment_status" => "open"];
        } else {
            $content = [
                "terms_names" => ["category" => $episodio->getCategoriaEpisodio()],
                "post_thumbnail" => (int)$imagem_destaque_id, "comment_status" => "open"
            ];
        }
        $themovie = $this->serie->theMovieDB;
        if(FuncoesUteis::identificar_links_embed($episodio->getLinkPronto())){
            $campo_video = "embediframe";
        }else{
            $campo_video = "videoembed";
        }
        $content["custom_fields"] = [
            ["key" => "animes", "value" => [(int)$temporada->post_id]],
            ["key" => "_animes", "value" => "field_5df9793237d5d"],
            ["key" => $campo_video, "value" => $episodio->getLinkPronto()],
            ["key" => "descricao", "value" => $episodio->descricao],
            ["key" => "numero_do_episodio", "value" => $episodio->episodio],
            ["key" => "tipo_audio", "value" => $episodio->tipo],
            ["key" => "duracao", "value" => "PT" . $themovie->episodio_duracao . "M00S"],
            ["key"=>"nome_episodio","value"=>$episodio->nome_episodio]
        ];
        $content["post_type"] = "episodios";
        return $content;
    }

    public function updateContentSerie(array $postEpisodios, array $anterior_content)
    {
        $result = $this->procurar_animes_episodio_array($anterior_content["custom_fields"]);
        $content["custom_fields"] = [];
        if (!empty($postEpisodios['DUBLADO'])) {
            if (key_exists("id_categoria_dublado", $result)) {
                $dado = $result["id_categoria_dublado"] . "," . implode(",", $postEpisodios['DUBLADO']);
            } else {
                $dado = implode(",", $postEpisodios['DUBLADO']);
            }
            $content["custom_fields"][] = ["key" => "id_categoria_dublado", "value" => $dado];
        }

        if (!empty($postEpisodios['LEGENDADO'])) {
            if (key_exists("id_categoria_legendado", $result)) {
                $dado = $result["id_categoria_legendado"] . "," . implode(",", $postEpisodios['LEGENDADO']);
            } else {
                $dado = implode(",", $postEpisodios['LEGENDADO']);
            }
            $content["custom_fields"][] = ["key" => "id_categoria_legendado", "value" => $dado];

        }
        return $content;
    }

    private function procurar_animes_episodio_array($array)
    {
        $result = [];
        foreach ($array as $a) {
            if ($a["key"] == "id_categoria_dublado") {
                $result[$a["key"]] = $a["value"];
            } else if ($a["key"] == "id_categoria_legendado") {
                $result[$a["key"]] = $a["value"];
            }
        }
        return $result;
    }


}
