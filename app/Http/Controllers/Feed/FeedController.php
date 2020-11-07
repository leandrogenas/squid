<?php

namespace App\Http\Controllers\Feed;

use App\Enums\TipoLinksFeed;
use App\Http\Controllers\Controller;
use App\Models\feed\ListFeed;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function ultimos_links(){
        $dados["ANIMESVIP"] = $this->getUltimosLinksArrayForTipo("ANIMESVIP");
        $dados["FILMESVIATORRENTBIZ"] = $this->getUltimosLinksArrayForTipo("FILMESVIATORRENTBIZ");
        $dados["FILMESVIATORRENTVIP"] = $this->getUltimosLinksArrayForTipo("FILMESVIATORRENTVIP");
        $dados["JOGOSTORRENT"] = $this->getUltimosLinksArrayForTipo("JOGOSTORRENT");
        $dados["SERIESONLINE"] = $this->getUltimosLinksArrayForTipo("SERIESONLINE");
        return view("screens.feed.pagina-ultimoslinks")->with(["dados"=>$dados]);
    }

    private function getUltimosLinksArrayForTipo($tipo){
        $dados = [];
        foreach (TipoLinksFeed::getValues() as $value){
            if($value != TipoLinksFeed::NIMBUS){
                $resultado = ListFeed::getUltimoLink($tipo,$value);
                if($resultado){
                    $dados[$value] = $resultado;
                }
            }
        }
        return $dados;
    }

}
