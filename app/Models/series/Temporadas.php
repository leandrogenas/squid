<?php


namespace App\Models\series;

use App\Models\filmes\TheMovieDB;
use App\Models\Imagens;
use App\Utils\FuncoesUteis;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use voku\helper\HtmlDomParser;

/**
 * Class Temporadas
 * @property Episodio[] episodios
 * @property Serie serie
 * @package App\Models\series
 */
class Temporadas
{

    public $episodios = [];
    public $serie;
    public $temporada_numero, $descricao, $url_the_movie_capa, $data_lancamento,$imagem_pronta,$post_id,$content;
    public $ja_existe_postagem = false;
    public $titulo;

    /**
     * Temporadas constructor.
     * @param Serie $serie
     */
    public function __construct(Serie $serie)
    {
        $this->serie = $serie;
    }

    public function preparar_informacoes(TheMovieDB $theMovieDB)
    {
        try {
            $data = $theMovieDB->getTemporadaDetalhes($this->temporada_numero);
            $this->url_the_movie_capa = "https://image.tmdb.org/t/p/w300_and_h450_bestv2" . $data->poster_path;
            $this->data_lancamento = $data->air_date;
            $this->descricao = $data->overview;
            if(empty($this->descricao) || is_null($this->descricao)){
                $this->descricao = $this->serie->descricao;
            }
            if(empty($data->poster_path)){
                $this->url_the_movie_capa = $this->serie->url_imagem_themovie;
            }
        } catch (\Throwable $ex) {
            \Log::error($ex);
            $this->url_the_movie_capa = $this->serie->url_imagem_themovie;
            $this->descricao = $this->serie->descricao;
        }
        $this->verificar_se_existe_postagem_e_pegar_id();
    }

    public function baixar_imagem()
    {
        $img = "img/temp.png";
        try{
            FuncoesUteis::baixar_imagem($this->url_the_movie_capa, $img);
        }catch (\Throwable $ex){
            $this->url_the_movie_capa = $this->serie->url_imagem_themovie;
            FuncoesUteis::baixar_imagem($this->url_the_movie_capa, $img);
        }
    }

    public function preparar_imagens_por_site($site)
    {
        $imagem_pronta = $this->preparar_imagens_serie("temporada-".$this->temporada_numero."-serieonline-pro.jpeg");
        $this->colocar_logo_imagem($imagem_pronta,$site);
        ImageOptimizer::optimize("img/baixadas/".$imagem_pronta);
    }

    private function preparar_imagens_serie($extensao_nome)
    {
        $nome = FuncoesUteis::limpar_caracteres_especiais($this->titulo);
        $nome_pronto = $nome.$extensao_nome;
        $this->imagem_pronta = $nome_pronto;
        return $nome_pronto;
    }

    private function colocar_logo_imagem($nome, $site)
    {
        Imagens::colocar_logo_somente_serie($site, public_path("img".DIRECTORY_SEPARATOR."temp.png"),
            public_path("img".DIRECTORY_SEPARATOR."baixadas".DIRECTORY_SEPARATOR."$nome"),true);
    }

    private function verificar_se_existe_postagem_e_pegar_id()
    {
//        $url = "https://seriesonline.pro/?s=" . urlencode($this->serie->titulo." temporada ".$this->temporada_numero);
//        $seriespro = HtmlDomParser::file_get_html($url);
//        $div_thumb = $seriespro->findOneOrFalse("div.video-thumb");
//        if ($div_thumb != false) {
//            $a = $div_thumb->findOne("a");
//            $link = $a->getAttribute("href");
//            $serie = HtmlDomParser::file_get_html($link);
//            $postagem = $serie->findOneOrFalse("link[rel='shortlink']");
//            if ($postagem != false) {
//                $link_com_id = $postagem->getAttribute("href");
//                $resultado = str_replace("https://seriesonline.pro/?p=", "", $link_com_id);
//                $this->post_id = $resultado;
//                $this->ja_existe_postagem = true;
//            }
//        } else {
//            $this->ja_existe_postagem = false;
//        }
       $this->ja_existe_postagem = false;
    }

    public function getTituloPronto(){
        return $this->serie->titulo." ".$this->temporada_numero."ª Temporada";
    }

}
