<?php


namespace App\Models\series;

use App\Enums\Sites;
use App\Models\filmes\Filme;

/**
 * Class AtualizaPostagem
 * @package App\Models\series
 * @property Filme $filme;
 */
class AtualizaPostagem
{
    private $post_id_info,$post_id_org,$post_id_pirate, $post_id_vip,$post_id_biz;
    public $filme;

    public function get_post_edit_por_site($site){
        switch ($site){
            case Sites::FILMESVIATORRENT_INFO:
                return $this->post_id_info;
            case Sites::FILMESVIATORRENT_ORG:
                return $this->post_id_org;
            case Sites::PIRATEFILMES_NET:
                return $this->post_id_pirate;
            case Sites::FILMESTORRENT_VIP:
                return $this->post_id_vip;
            case Sites::FILMESVIATORRENT_BIZ:
                return $this->post_id_biz;
            default:
                return "";
        }
    }

    public function set_post_edit($site,$post_id){
        switch ($site){
            case Sites::FILMESVIATORRENT_INFO:
                 $this->post_id_info = $post_id;
                 break;
            case Sites::FILMESVIATORRENT_ORG:
                $this->post_id_org = $post_id;
                break;
            case Sites::PIRATEFILMES_NET:
                $this->post_id_pirate = $post_id;
                break;
            case Sites::FILMESTORRENT_VIP:
                $this->post_id_vip = $post_id;
                break;
            case Sites::FILMESVIATORRENT_BIZ:
                $this->post_id_biz =$post_id;
                break;
            default:
                break;
        }
    }
}
