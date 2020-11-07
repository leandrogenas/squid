<?php


namespace App\Models\filmes;


use App\Models\filmes\download\LinkFilme;
use App\Models\filmes\download\LinksDownloads;
use App\Models\IMDB;
use App\Models\YouTube;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

/**
 * Class Lapumia
 * @property string $link
 * @property HtmlDomParser $dom
 * @property TheMovieDB $theMovieDB
 * @property IMDB $imdb
 * @package App\Models\filmes
 */
class Lapumia extends Filme
{
    private $link, $dom;

    /**
     * Lapumia constructor.
     * @param $link
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    public function carregar_dados()
    {
        $this->carregar_site();
        $this->carregar_dados_filme();
    }


    private function carregar_site()
    {
        $this->dom = HtmlDomParser::file_get_html($this->link);
    }

    private function carregar_dados_filme()
    {
        if(empty($this->movie_name)){
            $this->titulo_original = self::pegar_dados_e_verificar($this->dom, ["b:contains(Titulo Original)"]);
            $this->titulo_traduzido = $this->titulo_original;
        }else{
            $this->titulo_original = $this->movie_name;
            $this->titulo_traduzido = $this->movie_name;
        }
        $this->generos = $this->pegar_generos();
        $this->audio = self::pegar_dados_e_verificar($this->dom, ["b:contains(Idioma)"]);
        $this->audioFilme = self::identificar_audiofilme($this->audio);
        $this->legenda = self::pegar_dados_e_verificar($this->dom, ["b:contains(Legenda)"]);
        $this->qualidade = self::pegar_dados_e_verificar($this->dom, ["b:contains(Qualidade)"]);
        $this->qualidade_original = $this->qualidade;
        $this->qualidade = self::arrumar_qualidade($this->qualidade);
        $this->formato = self::pegar_dados_e_verificar($this->dom, ["b:contains(Formato)"]);
        $this->tamanho = self::pegar_dados_e_verificar($this->dom, ["b:contains(Tamanho)"]);
        $this->qualidade_audio = self::pegar_dados_e_verificar($this->dom, ["b:contains('Qualidade do Audio')"]);
        $this->qualidade_video = self::pegar_dados_e_verificar($this->dom, ["b:contains('Qualidade de Video')"]);
        $this->ano_lancamento = self::pegar_dados_e_verificar($this->dom, ["b:contains('Ano de Lançamento')"]);
        $this->duracao = self::pegar_dados_e_verificar($this->dom, ["b:contains('Duração')"]);
        $this->pegar_links_download();
        self::verificar_e_usar_themoviedb($this->theMovieDB, $this);
        self::usar_link_youtube($this);
        self::verificar_e_usar_imdb($this->imdb, $this);
        self::preparar_categorias($this);
        self::preparar_titulo_sites($this);
        self::preparar_imagens($this);
        self::preparar_dados_serie_ou_filme($this);
        self::verificar_se_e_imagem_cinema($this);
    }

    private function pegar_generos()
    {
        try {
            return self::pegar_dados_e_verificar($this->dom, ["b:contains(Gênero)"]);
        } catch (\Exception $ex) {
            \Log::error($ex);
            return "";
        }
    }

    private function pegar_links_download()
    {
        $spans = $this->dom->find("h2:contains(Versão)");
        if (count($spans) == 0) {
            $spans = $this->dom->find("h2:contains(':: NACIONAL ::')");
        }
        $links_de_download = [];
        $spans_adicionados = [];
        foreach ($spans as $span) {
            $p = $span->nextSibling();
            $a_links = $p->nextSibling()->getElementsByTagName("a");
            $lapumia_link = new LinksDownloads();
            $lapumia_link->texto_links = $span->text();
            $audio_identificado = self::identificar_audiofilme_texto($span->text());
            $links_filmes = [];
            $spans_adicionados[] = $span->text();
            foreach ($a_links as $link_a) {
                $link_attr = $link_a->getAttribute("href");
                if (Str::contains($link_attr, "magnet:?")) {
                    $link_magnetico = $link_attr;
                    $link_filme = new LinkFilme();
                    $qualidade_link = self::identificar_qualidade_link_por_imagem($link_a->getElementByTagName("img")->getAttribute("src"));
                    $link_filme->link = $link_magnetico;
                    $link_filme->qualidade_link = $qualidade_link;
                    $link_filme->audio_link = $audio_identificado;
                    $links_filmes[] = $link_filme;
                }
            }
            $lapumia_link->links = $links_filmes;
            $links_de_download[] = $lapumia_link;
        }
        $this->pegar_links_extras($this->dom,"h2:contains(FULL)",$links_de_download,$spans_adicionados);
        $this->pegar_links_extras($this->dom,"h2:contains(Dual)",$links_de_download,$spans_adicionados);
        $this->links_downloads = $links_de_download;
    }

    private function pegar_links_extras(HtmlDomParser $dom, $selector, array &$links_de_download, array &$spans_adicionados)
    {
        $spans = $dom->find($selector);
        foreach ($spans as $span) {
            $p = $span->nextSibling();
            if(!in_array($span->text(),$spans_adicionados)){
                $a_links = $p->nextSibling()->find("a");
                if(count($a_links) > 0){
                    $lapumia_link = new LinksDownloads();
                    $lapumia_link->texto_links = $span->text();
                    $audio_identificado = self::identificar_audiofilme_texto($span->text());
                    $links_filmes = [];
                    $spans_adicionados[] = $span->text();
                    foreach ($a_links as $link_a) {
                        $link_attr = $link_a->getAttribute("href");
                        if (Str::contains($link_attr, "magnet:?")) {
                            $link_magnetico = $link_attr;
                            $link_filme = new LinkFilme();
                            $qualidade_link = self::identificar_qualidade_link_por_imagem($link_a->getElementByTagName("img")->getAttribute("src"));
                            $link_filme->link = $link_magnetico;
                            $link_filme->qualidade_link = $qualidade_link;
                            $link_filme->audio_link = $audio_identificado;
                            $links_filmes[] = $link_filme;
                        }
                    }
                    $lapumia_link->links = $links_filmes;
                    $links_de_download[] = $lapumia_link;
                }
            }
        }
    }


}
