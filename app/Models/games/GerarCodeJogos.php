<?php


namespace App\Models\games;

/**
 * Class GerarCodeJogos
 * @package App\Models\games
 * @property Jogo $jogo
 */
class GerarCodeJogos
{
    private $jogo;

    /**
     * GerarCodeJogos constructor.
     * @param  Jogo  $jogo
     */
    public function __construct(Jogo $jogo)
    {
        $this->jogo = $jogo;
    }

    public function gerar_code_jogostorrent(){
        $content["custom_fields"] =[
            ["key" => "imagens_galeria", "value" => [(int)$this->jogo->img1_id,(int)$this->jogo->img2_id]],
            ["key"=>"_imagens_galeria", "value"=>"field_5abebf86a19c2"],
            ["key"=>"capasteam","value"=>$this->jogo->link_capa],
            ["key"=>"nfo","value"=> $this->jogo->nfo],
            ["key"=>"tamanho","value"=> $this->jogo->tamanho],
            ["key"=>"torrent","value"=> $this->jogo->link_magnetico],
            ["key"=>"video_trailer_custom","value"=> $this->jogo->link_trailer],
            ["key"=>"requisitos","value"=>$this->jogo->requisistos],
            ["key"=>"instalacao","value" => nl2br("<span style=\"color: #ff0000;\"><strong>IMPORTANTE</strong>:</span> ANTES DE INSTALAR O JOGO É NECESSÁRIO INSTALAR OS <a href=\"http://jogostorrents.site/programas-essenciais-para-os-jogos-rodarem/\" target=\"_blank\" rel=\"noopener\">PROGRAMAS ESSENCIAIS PARA SEUS JOGOS RODAREM</a>.

[1] - Baixe o Jogo por Torrent (<a href=\"http://jogostorrents.site/como-baixar-arquivos-torrents-e-baixar-no-site/\" target=\"_blank\" rel=\"noopener\">Como Baixar Torrents</a>) ou MultiLinks.

[3] -  Vá até o seu arquivo .ISO (<a href=\"http://jogostorrents.site/como-usar-arquivo-iso/\" target=\"_blank\" rel=\"noopener\">Como usar arquivos .ISO</a>) e Emule-o.

[4] - Após emular o arquivo .ISO irá aparecer um novo DVD para você em \"Meu Computador\".

[5] - Abra-o e execute o arquivo Setup.exe e instale seu jogo.

[6] - Após terminar de instalar o jogo, vá até o DVD emulado ou em seu arquivo .ISO,  procure por uma pasta com o nome da Release que lançou o jogo (Codex, Skidrow, CPY, Plaza).

[7] - Abra a pasta e copie todos os arquivo dessa pasta, e cole na pasta em que o seu jogo foi instalado.

[8] - Desative o Antivírus e o Windows Defender pois eles podem bloquear o jogo !

[9] - Após instalado, clique com o botão direito no ícone do jogo e \"EXECUTAR COMO ADMINISTRADOR\" (importante sempre abrir o jogo dessa forma).

[10] - Aproveite o jogo! Obrigado por visitar nosso site, lembre-se de que se gostar do jogo compre-o! e compartilhe nossa postagem no facebook para mais pessoas baixarem o jogo.

Ainda não sabe como instalar? Vá em \".NFO\" na aba \"DESCRIÇÃO\" e aprenda como instalar escrito pelo próprios criadores do conteúdo :)")]
        ];
        $this->jogo->custom_content = $content;
        $this->jogo->content = $this->jogo->campo_texto;
    }

}
