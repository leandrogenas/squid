<?php


namespace App\Models\games;


use Ixudra\Curl\Facades\Curl;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDom;
use voku\helper\UTF8;

class Steam
{
    public $link_steam,$titulo_steam,$descricao,$reqminimo,$reqmaximo,$link_capa,$appIDSteam,$generos,$imagens;

    /**
     * Steam constructor.
     * @param $link_steam
     */
    public function __construct($link_steam)
    {
        $this->link_steam = $link_steam;
        $this->reqmaximo = "";
    }


    public function carregar_dados(){
        $response = Curl::to($this->link_steam)
            ->withHeader("Cookie: birthtime=-127166399;lastagecheckage=21-0-1966")->withHeader("Accept-Language: pt-BR")->get();
        $dom = HtmlDomParser::str_get_html($response);
        $titulo_steam = $dom->findOne("div.apphub_AppName")->text();
        $titulo = trim(preg_replace('/[^A-Za-z0-9\s]/','' ,$titulo_steam));
        $this->titulo_steam = $titulo;
        $descricao = $dom->findOneOrFalse("div.game_description_snippet");
        if($descricao != false){
            $this->descricao = $descricao->text();
        }else{
            $this->descricao = str_replace("Sobre este jogo","",$dom->findOne("div#game_area_description")->text());
        }
        if(count($dom->find("div.game_area_sys_req_leftCol")) > 0){
            $this->reqminimo = $this->remover_html_requisitos($dom->findOne("div.game_area_sys_req_leftCol > ul")->html());
        }
        if(count($dom->find("div.game_area_sys_req_rightCol")) > 0){
            $this->reqmaximo = $this->remover_html_requisitos($dom->findOne("div.game_area_sys_req_rightCol > ul")->html());
        }
        if(count($dom->find("div.game_area_sys_req_full")) > 0){
            $this->reqminimo = $this->remover_html_requisitos($dom->findOne("div.game_area_sys_req_full > ul")->html());
        }
        $this->link_capa = $dom->findOne("div > div.game_header_image_ctn > img")->getAttribute("src");
        try{
            preg_match('/app\/(.*)\/.|app\/(.*)\//', $this->link_steam, $saida);
            $this->appIDSteam = $saida[1];
        }catch (\Exception $ex){
            \Log::error($ex);
            $this->appIDSteam = "";
        }
        $elementos = $dom->findOne("div.popular_tags")->getElementsByTagName("a");
        foreach ($elementos as $elemento){
            $this->generos[] = $elemento->text();
        }
        $this->pegar_imagens($dom);
    }

    private function pegar_imagens(HtmlDomParser $dom){
        $imgs = $dom->find("div.screenshot_holder > a");
        $limit = 2;
        $count = 0;
        foreach ($imgs as $img){
            $link = $img->getAttribute("href");
            $link = str_replace("1920x1080","600x338",$link);
            $this->imagens[] = $link;
            $count++;
            if($count == $limit){
                break;
            }
        }
    }

    private function remover_html_requisitos($texto){
//        $t = str_replace("<ul>","",$texto);
//        $t = str_replace("</ul>","",$t);
//        $t = str_replace("<br>","",$t);
//        $t = str_replace("<ul class=\"bb_ul\">","",$t);
//        $t = str_replace("<li>","",$t);
//        $t = str_replace("</li>","",$t);
//        $t = str_replace("<strong>","",$t);
//        $t = str_replace("</strong>","",$t);
        return strip_tags($texto);
    }
}
