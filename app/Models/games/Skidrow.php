<?php


namespace App\Models\games;

use App\Models\YouTube;
use App\Utils\FuncoesUteis;
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDom;

/**
 * Class Skidrow
 * @package App\Models\games
 * @property HtmlDomParser $dom
 * @property Steam $steam
 */
class Skidrow extends Jogo
{
    private $dom, $links_download, $link_site, $descricao, $steam;

    /**
     * Skidrow constructor.
     * @param $link_site
     */
    public function __construct($link_site)
    {
        $this->link_site = $link_site;
    }


    public function carregar_dados()
    {
        $this->carregar_site();
        $this->carregar_dados_skidrow_e_steam();
//        $this->carregar_nfo();
        $this->pegar_links_download();
        $this->verificar_pt_br();
        $this->verificar_sem_torrent();
        $this->verificar_todas_dlc_texto();
        $this->montar_texto();
        $this->baixar_imagens();
        $this->arrumar_requisitos();
    }

    private function carregar_site()
    {
        $this->dom = HtmlDomParser::file_get_html($this->link_site);
    }

    private function arrumar_requisitos()
    {
        $req = nl2br($this->requisistos);
        $resultado = preg_replace('/<br \/>\n<br \/>/', '<br>', $req);
        $resultado = preg_replace('/<br \/>\n<br \/>/', '<br>', $resultado);
        $resultado = preg_replace('/<br>\n<br \/>/', '<br>', $resultado);
        $this->requisistos = $resultado;
    }

    private function verificar_sem_torrent(){
        if($this->no_torrent){
            $texto_inicial = "<h2>ESSE JOGO NÃO TEM TORRENT! SÓ CONTÉM LINKS DIRETO! DESÇA ATÉ O FINAL DA POSTAGEM E ESCOLHA O LINK DE SUA PREFERENCIA</h2>";
            $texto_inicial .= $this->descricao;
            $this->descricao = $texto_inicial;
        }
    }

    private function verificar_pt_br()
    {
        if (!$this->is_pt_br) {
            $google = new GoogleTranslate();
            $google->setTarget("pt");
            $this->requisistos = $google->translate(html_entity_decode($this->requisistos));
            $this->descricao = $google->translate(html_entity_decode($this->descricao));
        } else {
            $this->titulo .= " [PT-BR]";
            $texto_inicial_descr = "<a href='https://jogostorrents.site/como-traduzir-e-crackear-um-jogo/' target='_blank' rel='noopener noreferrer'>VEJA AQUI COMO TRADUZIR O SEU JOGO</a>";
            $this->descricao = $texto_inicial_descr.$this->descricao;
        }
    }
    private function verificar_todas_dlc_texto(){
        if($this->todas_dlc){
            $texto_inicial = "<blockquote>Essa versão é independente (única) e contém o jogo completo e todas as DLCs e atualizações anteriores.</blockquote>";
            $texto_inicial .= $this->descricao;
            $this->descricao = $texto_inicial;
        }
    }

    private function carregar_dados_skidrow_e_steam()
    {
        if (count($this->dom->find("a:contains('steampowered')")) > 0) {
            $link_steam = $this->dom->findOne("a:contains('steampowered')")->getAttribute("href");
            $this->link_steam = $link_steam;
            $steam = new Steam($link_steam);
            $steam->carregar_dados();
            $this->requisistos = $steam->reqminimo."\n\n".$steam->reqmaximo;
            $this->generos = $steam->generos;
            $this->link_capa = $steam->link_capa;
            $this->titulo = $steam->titulo_steam;
            $this->descricao = $steam->descricao;
            $this->appIDSteam = $steam->appIDSteam;
            $this->steam = $steam;
        }
        $tamanho = $this->dom->findOne("p:contains('Size')")->text();
        preg_match('/Size:(.*)/', $tamanho, $saida);
        $tamanho = trim($saida[1]);
        $this->tamanho = $tamanho;
        $this->link_trailer = YouTube::pesquisar_jogo($this->titulo)[0];
    }

    private function carregar_nfo()
    {
        $this->nfo = "#";
    }

    private function montar_texto()
    {
        $texto_final = $this->descricao;
        $texto = "\n<iframe id='download'  src='http://store.steampowered.com/widget/".$this->appIDSteam."/' width='646' height='190' frameborder='0'></iframe><hr>";
        $texto .= "<p style='text-align: center'>Área de Download</p>\n<div class='container-download'>";
        //parada
        $texto_download = "";
        foreach ($this->links_download as $dado) {
            $texto_download .= $this->montar_download_texto($dado);
        }
        $texto .= $texto_download."</div>";
        $texto_final .= $texto;
        $this->campo_texto = $texto_final;

    }

    private function montar_download_texto(array $dado)
    {
        if($dado["texto"] === "PIXELDRA"){
            $d = "<div onclick='expandir(this);' style='background-color: #2196F3;' class='header'><span>".$this->preparar_texto_link($dado["texto"])." (RECOMENDADO)</span></div><div class='content'><ul>";
        }else{
            $d = "<div onclick='expandir(this);' class='header'><span>".$this->preparar_texto_link($dado["texto"])."</span></div><div class='content'><ul>";
        }
        if (array_key_exists("multi", $dado)) {
            foreach ($dado["multi"] as $multi) {
                $d .= "<li><a href='".$multi["link"]."' target='_blank' rel='nofollow noopener noreferrer'>".$multi["texto"]."</a></li>";
            }
        } else {
            $d .= "<li><a href='".$dado["link"]."' target='_blank' rel='nofollow noopener noreferrer'>".$dado["texto"]."</a></li>";
        }
        $d .= "</ul></div>";
        return $d;
    }

    public function preparar_texto_link($texto){
        if (trim($texto) == "UPLOADHAVEN"){
            return $texto." (recomendado)";
        }else{
            return $texto;
        }
    }

    private function baixar_imagens()
    {
        $titulo_imagem = FuncoesUteis::limpar_caracteres_especiais($this->steam->titulo_steam);
        $img1 = $titulo_imagem."-screenshot-1.jpeg";
        $img2 = $titulo_imagem."-screenshot-2.jpeg";
        FuncoesUteis::baixar_imagem($this->steam->imagens[0], "img/baixadas/".$img1);
        FuncoesUteis::baixar_imagem($this->steam->imagens[1], "img/baixadas/".$img2);
        \ImageOptimizer::optimize("img/baixadas/".$img1);
        \ImageOptimizer::optimize("img/baixadas/".$img2);
        $this->img_1 = $img1;
        $this->img_2 = $img2;
    }

    private function pegar_links_download()
    {
        $p = $this->dom->findMulti("p");
        $links_download = [];
        $foi_uma_vez = false;
        foreach ($p as $e) {
            $link_down = [];
            $link_multi = [];
            if (count($e->getElementsByTagName("span")) == 1 && count($e->getElementsByTagName("a")) == 1) {
                $href = $e->getElementByTagName("a")->getAttribute("href");
                $t = $e->getElementByTagName("span")->text();
                if (!$this->verificar_links_e_texto($href, $t)) {
                    $link_down["texto"] = $t;
                    $link_down["link"] = $href;
                    $links_download[] = $link_down;
                }
            } elseif (count($e->getElementsByTagName("span")) > 2 && count($e->getElementsByTagName("a")) > 1) {
                foreach ($e->getElementsByTagName("span") as $span) {
                    if ($span->getAttribute("style") == "color: #ecf22e;") {
                        if (!$this->verificar_links_e_texto("", $span->text())) {
                            if ($foi_uma_vez) {
                                $link_down["multi"] = $link_multi;
                                $links_download[] = $link_down;
                                $link_multi = [];
                            }
                            $link_down["texto"] = $span->text();
                            $foi_uma_vez = true;
                        }
                    }
                    try {
                        $proximo = $span->nextSibling();
                        while (true) {
                            if ($proximo->tag == "a") {
                                if (!$this->verificar_links_e_texto($proximo->getAttribute("href"), $proximo->text())) {
                                    $links["texto"] = $proximo->text();
                                    $links["link"] = $proximo->getAttribute("href");
                                    $link_multi[] = $links;
                                }
                            } elseif ($proximo->tag == "span") {
                                if ($proximo->getAttribute("style") == "color: #00ff00;") {
                                    break;
                                }
                            }
                            $proximo = $proximo->nextSibling();
                        }
                    } catch (\Throwable $ex) {

                    }
                }
            } elseif (count($e->getElementsByTagName("a")) > 1) {
                $link_down = [];
                $span = $e->getElementByTagName("span");
                $link_down["texto"] = $span->text();
                $links = [];
                if (!$this->verificar_links_e_texto($span->text())) {
                    foreach ($e->getElementsByTagName("a") as $a) {

                        $href = $a->getAttribute("href");
                        $t = $a->text();
                        $l["texto"] = $t;
                        $l["link"] = $href;
                        $links[] = $l;
                    }
                }
                $link_down["multi"] = $links;
                $links_download[] = $link_down;
            }
        }
        $this->links_download = $links_download;
    }

    private function verificar_links_e_texto($link, $texto = "")
    {
        $links = explode(",", \Config::get("sync.ignora_links_jogo"));
        foreach ($links as $l) {
            if (Str::contains($link, $l) || Str::contains($texto, $l)) {
                return true;
            }
        }
        return false;
    }
}
