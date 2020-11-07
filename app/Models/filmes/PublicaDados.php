<?php


namespace App\Models\filmes;


use App\Enums\Sites;
use App\Models\games\Jogo;
use App\Models\series\AtualizaPostagem;
use App\Models\wordpress\WordPress;
use Illuminate\Support\Facades\Session;
use voku\helper\HtmlDomParser;

class PublicaDados
{
    public function publicarFilmes(WordPress $wordPress,$progresso_name,$filmes = [], $sites = [] )
    {
        /**@var Filme $filme*/
        foreach ($filmes as $filme){
            $this->atualizar_status($progresso_name,"Buscando dados no site");
            $filme->carregar_dados();
            foreach ($sites as $site){
                $this->atualizar_site_progressso($progresso_name,$site);
                if($site != Sites::FILMESVIATORRENT_BIZ && $site != Sites::TORRENT_VIP && $site != Sites::KFILMES){
                    $this->publicar_demais_sites($filme,$wordPress,$progresso_name,$site);
                }else{
                    $this->publicar_filmesbizETorrent($filme,$wordPress,$progresso_name,$site);
                }
            }
            $this->atualizar_site_progressso($progresso_name,0);
            $this->atualizar_filme_publicado($progresso_name,$filme->titulo);
        }
    }

    private function publicar_filmesbizETorrent(Filme $filme,WordPress $wordPress,$progresso_name,$site){
        $this->atualizar_status($progresso_name,"Gerando code para o site");
        $filme->gera_code_por_site($site);
        $this->atualizar_status($progresso_name,"Fazendo a postagem");
        $post_id = $wordPress->addPostPorSite($site,$filme);
    }

    private function publicar_demais_sites(Filme $filme,WordPress $wordPress,$progresso_name,$site){
        $this->atualizar_status($progresso_name,"Preparando imagem para o site");
        $filme->preparar_imagens_por_site($site);
        $this->atualizar_status($progresso_name,"Fazendo upload da imagem");
        $resultado = $wordPress->uploadImagem($site,$filme->get_imagem_por_site($site));
        $imagem_id = $this->imagem_destacada($site,$resultado["id"]);
        $filme->img_url_upload = $resultado["url"];
        $this->atualizar_status($progresso_name,"Preparando code para o site: ".$site);
        $filme->gera_code_por_site($site);
        $this->atualizar_status($progresso_name,"Fazendo a postagem");
        $wordPress->addPostPorSite($site,$filme,$imagem_id);
    }

    private function imagem_destacada($site, $imagem_id){
        switch ($site){
            case Sites::FILMESVIATORRENT_ORG:
            case Sites::FILMESTORRENT_VIP:
                return $imagem_id;
            default:
                return null;
        }
    }
    private function atualizar_status($progresso_name,$status){
        Session::put($progresso_name,$status);
        Session::save();
    }

    private function atualizar_filme_publicado($progresso_name,$status){
        Session::put("$progresso_name-filme",$status);
        Session::save();
    }

    private function atualizar_site_progressso($progresso_name, $site){
        Session::put("$progresso_name-site",$site);
        Session::save();
    }

    public function publicarSerie(WordPress $wordPress,$progresso_name,$filmes = [], $sites = [] )
    {
        /**@var Filme $filme*/
        foreach ($filmes as $filme){
            $this->atualizar_status($progresso_name,"Buscando dados no site");
            $filme->carregar_dados();
            foreach ($sites as $site){
                $this->atualizar_site_progressso($progresso_name,$site);
                if($site != Sites::FILMESVIATORRENT_BIZ){
                    $this->publicar_demais_sites($filme,$wordPress,$progresso_name,$site);
                }else{
                    $this->publicar_filmesbizETorrent($filme,$wordPress,$progresso_name,$site);
                }
            }
            $this->atualizar_site_progressso($progresso_name,0);
            $this->atualizar_filme_publicado($progresso_name,$filme->titulo);
        }
    }

    /**
     * @param  WordPress  $wordPress
     * @param $progresso_name
     * @param  AtualizaPostagem[]  $atualiza_postagem
     * @param  array  $sites
     */
    public function atualizar_serie(WordPress $wordPress,$progresso_name, $atualiza_postagem = [],$sites = []){
        foreach ($atualiza_postagem as $atualiza){
            $this->atualizar_status($progresso_name,"Buscando dados no site");
            $filme = $atualiza->filme;
            $filme->carregar_dados();
            foreach ($sites as $site){
                $this->atualizar_site_progressso($progresso_name,$site);
                if($site != Sites::FILMESVIATORRENT_BIZ){
                    $this->atualizar_demais_sites($wordPress,$progresso_name,$filme,$atualiza->get_post_edit_por_site($site),$site);
                }else{
                    $this->atualizar_no_biz($wordPress,$progresso_name,$filme,$atualiza->get_post_edit_por_site($site),$site);
                }
            }
            $this->atualizar_site_progressso($progresso_name,0);
            $this->atualizar_filme_publicado($progresso_name,$filme->titulo);
        }
    }

    private function atualizar_demais_sites(WordPress $wordPress,$progresso_name, Filme $filme,$post_id,$site){
        $this->atualizar_status($progresso_name,"Pegando dados para atualização");
        $content = $wordPress->get_post_por_site($post_id,$site)["post_content"];
        $imagem_url = HtmlDomParser::str_get_html($content)->findOne("img")->getAttribute("src");
        $filme->img_url_upload = $imagem_url;
        $this->atualizar_status($progresso_name,"Gerando code atualizado");
        $filme->gera_code_por_site($site);
        $this->atualizar_status($progresso_name,"Atualizando a postagem");
        $wordPress->editPostPorSite($site,$post_id,$filme);
    }

    private function atualizar_no_biz(WordPress $wordPress,$progresso_name, Filme $filme,$post_id,$site){
        $this->atualizar_status($progresso_name,"Gerando Code para o biz");
        $filme->gera_code_por_site($site);
        $this->atualizar_status($progresso_name,"Excluindo postagem");
        $wordPress->excluir_post_biz($post_id);
        $this->atualizar_status($progresso_name,"Publicando nova postagem");
        $wordPress->addPostPorSite($site,$filme);
    }

    public function publicarJogos(WordPress $wordPress,$progresso_name,$jogos = [], $sites = []){
        /**@var Jogo[] $jogos*/
        foreach ($jogos as $jogo){
            $this->atualizar_status($progresso_name,"Buscando dados no site");
            $jogo->carregar_dados();
            foreach ($sites as $site){
                $this->atualizar_site_progressso($progresso_name,$site);
                $this->atualizar_status($progresso_name,"Preparando imagem");
                $jogo->img1_id = $wordPress->uploadImagem($site,$jogo->img_1)["id"];
                $jogo->img2_id = $wordPress->uploadImagem($site,$jogo->img_2)["id"];
                $this->atualizar_status($progresso_name,"Gerando Code");
                $jogo->gerar_code_por_site($site);
                $this->atualizar_status($progresso_name,"Fazendo a postagem");
                $resultado = $wordPress->addPostPorSiteJogo($site,$jogo);
            }
            $this->atualizar_site_progressso($progresso_name,0);
            $this->atualizar_filme_publicado($progresso_name,$jogo->titulo);
        }
    }
}
