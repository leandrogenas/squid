<?php


namespace App\Models\animes\Utils;


use App\Enums\Sites;
use App\Models\animes\Animes;
use App\Models\firebase\FirebaseUtil;
use App\Models\wordpress\WPress;

class FazerPostagem
{
    public $log;

    public function postar_animes($animes = [])
    {
        /**@var Animes $anime */
        $wp = new WPress();
        foreach ($animes as $anime) {
            try {
                if (is_null($anime->post_vip_id)) {
                    $anime->baixarImagem();
                    $this->log .= $anime->log;
                    $resultado = $wp->uploadImagemPorSite(Sites::ANIMESONLINE_VIP, $anime->imagem_pronta);
                    $imagem_id = $resultado["id"];
                    $content = PreparaDadosPostagem::prepararDadosAnime($anime, $imagem_id);
                    $id = $wp->addPostPorSite(Sites::ANIMESONLINE_VIP, $anime->titulo, $anime->descricao, $content);
                    $anime->post_vip_id = $id;
                    $this->log .= $anime->log;
                    $this->postar_episodios($anime, $wp);
                } else {
                    $this->postar_episodios($anime, $wp);
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve um erro: " . $ex->getMessage();
            }
        }
    }

    /**
     * @param Animes $anime
     * @param WPress $wp
     */
    private function postar_episodios(Animes $anime, WPress $wp)
    {
        $episodios = $anime->episodios;
        foreach ($episodios as $episodio) {
            $episodio->preparaDados();
            $imagem_result = $episodio->baixar_imagem();
            if(!$imagem_result){
                if(!empty($anime->link_falha)){
                    $this->log .= "Houve uma falha no link, tentando link alternativo. (link de falha) ".$anime->link_falha."\n";
                    $episodio->preparaDados(true,$anime->link_falha);
                    $imagem_result = $episodio->baixar_imagem();
                }
            }
            $episodio->episodio_anime = $anime->post_vip_id;
            $this->log .= $episodio->log;
            if (!$imagem_result) {
//                    $imagem_id = "28723";
//                    $episodio->episodio_capa = "https://animesonline.vip/wp-content/uploads/2018/12/thumb_default.jpg";
                $this->log .= "Não foi possível postar: " . $episodio->titulo . " pois não foi possível baixar a imagem do episódio";
            } else {
                $resultado = $wp->uploadImagemPorSite(Sites::ANIMESONLINE_VIP, $episodio->imagem_pronta);
                $imagem_id = $resultado["id"];
                $imagemUrl = $resultado['url'];
                $episodio->episodio_capa = $resultado["url"];
                $this->log .= $episodio->log;
                $content = PreparaDadosPostagem::preparaDadosEpisodio($episodio, $imagem_id);
                $post_id = $wp->addPostPorSite(Sites::ANIMESONLINE_VIP, $episodio->titulo, $episodio->descricao, $content, $episodio->post_date);
                if ($post_id) {
                    $body = "Acabou de sair o episódio " . $episodio->episodio_numero . ", não perca tempo! venha conferir 😉";
                    $this->log .= FirebaseUtil::enviarNotificacao(strval($anime->post_vip_id), $episodio->titulo, $body, $imagemUrl);
                }
            }
        }
    }
}
