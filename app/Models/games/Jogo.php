<?php


namespace App\Models\games;


class Jogo
{
    public $titulo, $tamanho, $requisistos, $como_instalar, $link_magnetico, $nfo, $link_steam, $appIDSteam, $link_trailer, $campo_texto, $link_capa, $img_1, $img_2, $generos,$is_pt_br,$img1_id, $img2_id, $no_torrent,$todas_dlc;
    public $custom_content,$content;

    public function carregar_dados(){
        //filhos devem implementar
    }

    public function gerar_code_por_site($site){
        $gerar_code = new GerarCodeJogos($this);
        $gerar_code->gerar_code_jogostorrent();
    }
}
