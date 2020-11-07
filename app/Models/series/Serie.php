<?php


namespace App\Models\series;


use App\Enums\Sites;
use App\Models\filmes\TheMovieDB;
use App\Models\Imagens;
use App\Models\series\Utils\FuncoesUteisSerie;
use App\Utils\FuncoesUteis;

/**
 * Class Serie
 * @property Temporadas[] temporadas
 * @property ConfigLinksDownload configLinkDownload
 * @package App\Models\series
 */
abstract class Serie extends FuncoesUteisSerie
{
    public $titulo,$titulo_traduzido,$titulo_preparado, $links_download, $descricao,$url_imagem_themovie,$img_site,$categorias_separadas,$content,$post_pai,$is_postar_serie,$audio;
    public $temporadas = [];
    public $configLinkDownload;
    public $serie_name = "";
    /**
     * @var Episodio[]
     */
    public $episodios;
    /**
     * @var TheMovieDB
     */
    public $theMovieDB;

    public function getTituloPronto(){
        return $this->titulo;
    }

    public abstract function pegar_links_para_episodio();

    public function baixar_imagem()
    {
        $img = "img/temp.png";
        FuncoesUteis::baixar_imagem($this->url_imagem_themovie, $img);
    }

    public abstract function carregar_dados();

    public function preparar_imagens_por_site($site)
    {
       $imagem_pronta = $this->preparar_imagens_serie("-serieonline-pro.jpeg");
       $this->colocar_logo_imagem($imagem_pronta,$site);
    }

    private function preparar_imagens_serie($extensao_nome)
    {
        $nome = FuncoesUteis::limpar_caracteres_especiais($this->titulo);
        $nome_pronto = $nome.$extensao_nome;
        $this->img_site = $nome_pronto;
        return $nome_pronto;
    }

    private function colocar_logo_imagem($nome, $site)
    {
        Imagens::colocar_logo_somente_serie($site, public_path("img".DIRECTORY_SEPARATOR."temp.png"),
            public_path("img".DIRECTORY_SEPARATOR."baixadas".DIRECTORY_SEPARATOR."$nome"),true);
    }

    public function getCategoriasParaTemporada(){
        $this->categorias_separadas[] = strtolower($this->titulo);
        return $this->categorias_separadas;
    }
}
