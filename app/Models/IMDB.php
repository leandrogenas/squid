<?php


namespace App\Models;


use App\Utils\FuncoesUteis;
use PHPUnit\Exception;
use voku\helper\HtmlDomParser;

class IMDB
{
    public $link,$id, $nota,$data,$link_capa, $titulo,$country,$votos,$rated,$tipo;

    /**
     * IMDB constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->link = "https://www.imdb.com/title/".$id."/";
    }


    public function pegar_dados_filme()
    {
        $json_resposta = HtmlDomParser::file_get_html("http://www.omdbapi.com/?i=".$this->id."&plot=full&apikey=bcecdcd9")->text();
        $dados = json_decode($json_resposta);
        $this->nota = $dados->imdbRating ?? "-";
        $this->country = $dados->Country ?? "";
        $this->votos = $dados->imdbVotes ?? "";
        $this->rated = $dados->Rated ?? "";
        $this->titulo = $dados->Title;
        $this->link_capa = $dados->Poster;
        $this->tipo = $dados->Type;
    }

    private static function procurar_dados($nome, $type = "movie"){
        try{
            $json_resposta = HtmlDomParser::file_get_html("http://www.omdbapi.com/?t=".urlencode($nome)."&plot=full&apikey=bcecdcd9&type=".$type)->text();
            $dados = json_decode($json_resposta);
            $arr["nome"] = $dados->Title ?? "";
            $arr["id"] = $dados->imdbID;
            return $arr;
        }catch (\Exception $ex){
            \Log::error($ex);
            $arr["nome"] = "Nenhum ID encontrado para o nome: $nome";
            $arr["id"] = "";
            return $arr;
        }catch (\Throwable $ex){
            \Log::error($ex);
            $arr["nome"] = "Nenhum ID encontrado para o nome: $nome";
            $arr["id"] = "";
            return $arr;
        }
    }

    public static function procurar_filme($nome_filme){
        return self::procurar_dados($nome_filme);
    }

    public static function procurar_serie($nome_serie){
        return self::procurar_dados($nome_serie, "series");
    }

    public function pegar_imagem_capa_e_nome(){
        try{
            $json_resposta = HtmlDomParser::file_get_html("http://www.omdbapi.com/?i=".$this->id."&plot=full&apikey=bcecdcd9")->text();
            $dados = json_decode($json_resposta);
            $this->link_capa = $dados->Poster;
            $this->titulo = $dados->Title;
        }catch (Exception $ex){
            \Log::error($ex);
        }catch (\Throwable $ex){
            \Log::error($ex);
        }
    }
}
