<?php


namespace App\Models\filmes\sites;


use App\Http\Controllers\TestController;
use App\Models\filmes\download\LinkFilme;
use App\Models\filmes\download\LinksDownloads;
use App\Models\filmes\Filme;
use App\Models\filmes\TheMovieDB;
use App\Models\IMDB;
use App\Utils\FuncoesUteis;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

/**
 * Class ComandoTorrent
 * @property string $link
 * @property HtmlDomParser $dom
 * @property TheMovieDB $theMovieDB
 * @property IMDB $imdb
 * @package App\Models\filmes
 */
class ComandoTorrent extends Filme
{
    private $link, $dom;
    public $html;

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
        if(!empty($this->html)){
            $html = $this->html;
            $html = str_replace('"',"'",$html);
            $html = str_replace('$','',$html);
            $this->dom = HtmlDomParser::str_get_html($html);
        }else{
            $this->carregar_site();
        }
        $this->carregar_dados_filme();
    }

    public function carregar_dados_html($html)
    {
        $regex = '/<p(?:[^\n]*(\n+))+<\/article>/m';
//        $new_html = "<html><body>";
//        preg_match_all($regex, $html, $matches, PREG_SET_ORDER, 0);
//        foreach ($matches as $match){
//            $new_html .= $match[0]."\n";
//        }
//        $new_html .= "</body></html>";
//        \Log::debug($new_html);
//        $html = "<html></html>";
//        dump(addslashes($html));
//        dump($html);
//        $new_html = "<html><body>";
//        $iniciar = false;
//        foreach ($html as $h) {
//            if(Str::contains($h,"<b>")|| Str::contains($h,"<p") || Str::contains($h,"<h2")){
//                $new_html .= str_replace('"',"'",$h)."\n";
//            }
//        }
//        $new_html .= "</body></html>";
//        $new_html = str_replace('"',"",$new_html);
//        dump($new_html);
//
        $html = str_replace('"',"'",$html);
        $html = str_replace('$','',$html);

        $new_html = "";
//        \Log::debug($html);
        dump($new_html);
        $this->dom = HtmlDomParser::str_get_html($html);
        //$this->carregar_site();
        $this->carregar_dados_filme();
    }

    private function carregar_site()
    {
        $this->dom = HtmlDomParser::file_get_html($this->link);
    }

    private function carregar_dados_filme()
    {
        if (empty($this->movie_name)) {
            $this->titulo_original = FuncoesUteis::limpar_caracteres_especiais_bludv(self::pegar_dados_e_verificar($this->dom, ["b:contains('Original')"]));
        } else {
            $this->titulo_original = $this->movie_name;
        }
        $this->titulo_traduzido = FuncoesUteis::limpar_caracteres_especiais_bludv(self::pegar_dados_e_verificar($this->dom, ["b:contains('Traduzido')"]));
        $this->generos = $this->pegar_generos();
        $this->audio = self::pegar_dados_e_verificar($this->dom, ["b:contains(Áudio)"]);
        $this->audioFilme = self::identificar_audiofilme($this->audio);
        $this->legenda = self::pegar_dados_e_verificar($this->dom, ["b:contains(Legenda)"]);
        $this->qualidade = self::pegar_dados_e_verificar($this->dom, ["b:contains(Qualidade)"]);
        $this->qualidade_original = str_replace(",", " &", $this->qualidade);
        $this->qualidade = FuncoesUteis::multipleReplace([",", " ", "|", ":"], "", $this->qualidade);
        $this->qualidade = self::arrumar_qualidade($this->qualidade);
        $this->formato = trim(FuncoesUteis::multipleReplace([':'], "", self::pegar_dados_e_verificar($this->dom, ["b:contains(Formato)"])));
        $this->tamanho = self::pegar_dados_e_verificar($this->dom, ["b:contains(Tamanho)"]);
        $this->qualidade_audio = self::pegar_dados_e_verificar($this->dom, ["b:contains('Qualidade de Áudio:')", "b:contains('Qualidade de Áudio e Vídeo:')"], [], "10");
        $this->qualidade_video = self::pegar_dados_e_verificar($this->dom, ["b:contains('Qualidade de Vídeo:')", "b:contains('Qualidade de Áudio e Vídeo:')"], [], "10");

        $this->verificar_ano_lancamento();
        $this->duracao = self::pegar_dados_e_verificar($this->dom, ["b:contains('Duração')"]);
        if ($this->is_serie) {
            $this->carregar_dados_serie();
            $this->pegar_links_download_serie();
        } else {
            $this->pegar_links_download();
        }
        self::verificar_e_usar_themoviedb($this->theMovieDB, $this);
        self::usar_link_youtube($this);
        self::verificar_e_usar_imdb($this->imdb, $this);
        self::preparar_categorias($this);
        self::preparar_titulo_sites($this);
        self::preparar_imagens($this);
        self::preparar_dados_serie_ou_filme($this);
        self::verificar_se_e_imagem_cinema($this);
    }

    private function carregar_dados_serie()
    {
        $titulo_serie = $this->dom->findOne("h1.entry-title")->text();
        $this->serie_temporada = FuncoesUteis::identificar_temporada_serie($titulo_serie);
        $this->pegar_texto_serie_anotacao();
    }

    private function pegar_texto_serie_anotacao()
    {
        $textos = $this->dom->find("span[style='color: #ff0000;']");
        $executou = false;
        $html = "<span style='color:red'>";
        foreach ($textos as $texto) {
            if (!is_null($texto->nextSibling())) {
                if (count($texto->nextSibling()->find("br")) > 0) {
                    $html .= $texto->text() . "<br>";
                    $executou = true;
                }
            }
        }
        $html .= "</span>";
        $this->serie_anotacao = $executou == true ? $html : "";
    }

    private function verificar_ano_lancamento()
    {
        try {
            if (count($this->dom->findMulti("b:contains('Lançamento')")) > 0) {
                $this->ano_lancamento = $this->dom->findOne("b:contains('Lançamento')")->nextSibling()->nextSibling()->text();
            } elseif (count($this->dom->findMulti("strong:contains('Lançamento')")) > 0) {
                $this->ano_lancamento = $this->dom->findOne("strong:contains('Lançamento')")->nextSibling()->text();
            } else {
                $this->ano_lancamento = "2019";
            }
        } catch (\Throwable $ex) {
            \Log::error($ex);
            $this->ano_lancamento = "2019";
        }

    }

    private function pegar_generos()
    {
        try {
            return FuncoesUteis::multipleReplace([':', " "], "", self::pegar_dados_e_verificar($this->dom, ["b:contains(Gênero)"]));
        } catch (\Exception $ex) {
            \Log::error($ex);
            return "";
        }
    }

    private function pegar_links_download()
    {
        $links_a = $this->dom->findMulti("a[href*='magnet']");
        $links_de_download = [];
        foreach ($links_a as $link_a) {
            if ($link_a->parentNode()->tag != "p") {
                $parent = $link_a->parentNode()->parentNode();
            } else {
                $parent = $link_a->parentNode();
            }
            $texto = $parent->text();
            $audio_identificado = self::identificar_audiofilme_texto($texto);
            $links_download = new LinksDownloads();
            $links_download->texto_links = $parent->text();
            $links = new LinkFilme();
            $links->link = $link_a->getAttribute("href");
            $links->qualidade_link = self::identificar_qualidade_link_texto($texto);
            $links->audio_link = $audio_identificado;
            $l = [$links];
            $links_download->links = $l;
            $links_de_download[] = $links_download;
        }
        $this->links_downloads = $links_de_download;
    }

    private function identificar_qualidade_link_texto($texto)
    {
        $t = strtolower($texto);
        if (Str::contains($t, "720p")) {
            return "720p";
        } elseif (Str::contains($t, "1080p")) {
            return "1080p";
        } elseif (Str::contains($t, "4k")) {
            return "4k";
        } else {
            return "";
        }
    }

    private function pegarSpans()
    {
        $lista_span = [];
        $tags = ["span", "strong", "h2 > strong"];
        $selectors = [":contains('Versão')", ":contains('::')", ":contains('#8212;')"];
        foreach ($tags as $tag) {
            foreach ($selectors as $selector) {
                $css_seletor = $tag . $selector;
                $spans = $this->dom->findMultiOrFalse($css_seletor);
                if ($spans != false) {
                    foreach ($spans as $span) {
                        $lista_span[] = $span->text();
                    }
                }
            }
        }
        $spans = $this->dom->findMultiOrFalse("h2 > strong");
        if ($spans != false) {
            foreach ($spans as $span) {
                $lista_span[] = $span->text();
            }
        }
        return $lista_span;
    }

    private function pegar_links_download_serie()
    {
        $lista_span_texto = $this->pegarSpans();
//            $spans = $this->dom->findMultiOrFalse("span:contains('Versão')");
//            if ($spans == false) {
//                $check_spans = ["::", "#8212;"];
//                $this->check_spans($spans, $check_spans, ["strong", "span"]);
//            }
//            if ($spans == false) {
//                $spans = $this->dom->findMultiOrFalse("h2 > strong");
//            }
        $dados = [];
        $count_span = 0;
        $links = $this->dom->findMulti("a[href*='magnet:?']");
        $lista_episodio = "";
        $texto_anterior_div = "";
        $total_span = count($lista_span_texto);
        foreach ($links as $link) {
            try {
                $texto_anterior = "|";
                $elemento_texto = $link->previousSibling();
                while (!Str::contains($texto_anterior, "Ep")) {
                    try {
                        $texto_anterior = $elemento_texto->text();
                        $elemento_texto = $elemento_texto->previousSibling();
                    } catch (\Throwable $ex) {
                        $texto_anterior = "Temporada Completa";
                        break;
                    }
                }
                if (count($link->find("img")) > 0) {
                    $qualidade = $this->identificar_qualidade_por_imagem($link->findOne("img")->getAttribute("src"));
                    if ($qualidade == false) {
                        $p_com_texto = $link->parent()->previousSibling()->previousSibling();
                        $qualidade = $this->identificar_qualidade_por_imagem($p_com_texto->text());
                    }
                    $texto_div = $texto_anterior;
                    $texto_link = $qualidade;
                    $texto_para_verificar = $texto_div . $qualidade . $texto_link;
                    if (Str::contains($texto_anterior_div, "Ep")) {
                        if ($total_span > ($count_span + 1)) {
                            $count_span++;
                        }
                    }
                } else {
                    $texto_div = $this->remover_texto_links($texto_anterior);
                    $texto_link = $link->text();
                    $texto_para_verificar = $texto_div . $texto_link;
                    if (Str::contains($texto_para_verificar, " e")) {
                        $resultado = trim(preg_replace('/e.*/', '', $texto_para_verificar));
                        if (Str::contains($lista_episodio, $resultado)) {
                            $count_span++;
                            $lista_episodio = "";
                        }
                    }
                }
                if (Str::contains($lista_episodio, $texto_para_verificar)) {
                    $count_span++;
                    $lista_episodio = "";
                } elseif (Str::contains($texto_anterior_div, "Temporada") && !Str::contains($texto_para_verificar,
                        "Temporada")) {
                    $count_span++;
                    $lista_episodio = "";
                } elseif (Str::contains($texto_para_verificar,
                        "ao") && !Str::contains($lista_episodio, "ao")) {
                    $remove_ao = trim(preg_replace('/ao.*/', '', $texto_para_verificar));
                    if (Str::contains($lista_episodio, $remove_ao)) {
                        $count_span++;
                        $lista_episodio = "";
                    }
                } elseif ($texto_anterior_div !== $texto_div) {
                    if (Str::contains($lista_episodio, $texto_div)) {
                        $count_span++;
                        $lista_episodio = "";
                    }
                }
                $lista_episodio .= " " . $texto_para_verificar;
                if ($total_span >= $count_span) {
                    $dados[$this->remover_caracteres($lista_span_texto[$count_span])][$texto_div][$texto_link] = $link->getAttribute("href");
                    $texto_anterior_div = $texto_div;
                }
            } catch (\Throwable $ex) {

            }
        }
        $this->links_download_serie = $dados;
    }

    private function identificar_qualidade_por_imagem($link_img)
    {
        if (Str::contains($link_img, "1080")) {
            return "1080p";
        } elseif (Str::contains($link_img, "720")) {
            return "720p";
        } elseif (Str::contains($link_img, "480")) {
            return "480p";
        }
        return false;
    }

    private function remover_texto_links($texto)
    {
        preg_match('/(.*):/', $texto, $resultado);
        return isset($resultado[1]) ? $resultado[1] : $texto;
    }

    private function remover_caracteres($texto)
    {
        return trim(FuncoesUteis::multipleReplace(["::", "&#8212;", "—"], "", $texto));
    }

}
