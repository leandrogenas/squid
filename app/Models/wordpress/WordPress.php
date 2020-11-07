<?php


namespace App\Models\wordpress;


use App\Enums\Sites;
use App\Models\filmes\Filme;
use App\Models\games\Jogo;
use App\Models\series\Episodio;
use App\Models\series\Serie;
use HieuLe\WordpressXmlrpcClient\WordpressClient;
use Illuminate\Support\Carbon;

/**
 * Class WordPress
 * @property WordpressClient $wpClienteFilmesInfo
 * @property WordpressClient $wpClienteFilmesOrg
 * @property WordpressClient $wpClienteFilmesBiz
 * @property WordpressClient $wpClientePirate
 * @property WordpressClient $wpClientTorrentVip
 * @property WordpressClient $wpSeriesOnlinePro
 * @package App\Models\wordpress
 */
class WordPress
{
    private $wpClienteFilmesInfo, $wpClienteFilmesOrg, $wpClientePirate, $wpClienteFilmesBiz, $wpClientTorrentVip,$wpClientFilmesTorrentVip, $wpJogosTorrent, $wpSeriesOnlinePro,$wpKfilmes;
    private $autenticado_filmes_info, $autenticado_filmes_org, $autenticado_pirate, $autenticado_filmes_biz, $autenticado_torrent_vip, $autenticado_jogos_torrent,$autenticado_filmesviatorrent_vip,$autenticado_seriesonline_pro,$autenticado_kfilmes;


    private function prepara_filmesviatorrent_info()
    {
        $user = \Config::get("sync.login_filmesviatorrentinfo.user");
        $password = \Config::get("sync.login_filmesviatorrentinfo.password");
        $url = \Config::get("sync.login_filmesviatorrentinfo.url");
        $this->wpClienteFilmesInfo = new WordpressClient();
        $this->wpClienteFilmesInfo->setCredentials($url, $user, $password);
    }

    private function prepara_kfilmes(){
        $user = \Config::get("sync.login_kfilmestorrent.user");
        $password = \Config::get("sync.login_kfilmestorrent.password");
        $url = \Config::get("sync.login_kfilmestorrent.url");
        $this->wpKfilmes = new WordpressClient();
        $this->wpKfilmes->setCredentials($url, $user, $password);
    }

    private function prepara_filmesviatorrent_org()
    {
        $user = \Config::get("sync.login_filmesviatorrentorg.user");
        $password = \Config::get("sync.login_filmesviatorrentorg.password");
        $url = \Config::get("sync.login_filmesviatorrentorg.url");
        $this->wpClienteFilmesOrg = new WordpressClient();
        $this->wpClienteFilmesOrg->setCredentials($url, $user, $password);
    }

    private function prepara_filmesviatorrent_biz()
    {
        $user = \Config::get("sync.login_filmesviatorrentbiz.user");
        $password = \Config::get("sync.login_filmesviatorrentbiz.password");
        $url = \Config::get("sync.login_filmesviatorrentbiz.url");
        $this->wpClienteFilmesBiz = new WordpressClient();
        $this->wpClienteFilmesBiz->setCredentials($url, $user, $password);
    }

    private function prepara_torrent_vip()
    {
        $user = \Config::get("sync.login_torrentvip.user");
        $password = \Config::get("sync.login_torrentvip.password");
        $url = \Config::get("sync.login_torrentvip.url");
        $this->wpClientTorrentVip = new WordpressClient();
        $this->wpClientTorrentVip->setCredentials($url, $user, $password);
    }

    private function prepara_seriesonlinepro()
    {
        $user = \Config::get("sync.login_seriesonlinepro.user");
        $password = \Config::get("sync.login_seriesonlinepro.password");
        $url = \Config::get("sync.login_seriesonlinepro.url");
        $this->wpSeriesOnlinePro = new WordpressClient();
        $this->wpSeriesOnlinePro->setCredentials($url, $user, $password);
    }

    private function prepara_filmesviatorrent_vip()
    {
        $user = \Config::get("sync.login_filmestorrentvip.user");
        $password = \Config::get("sync.login_filmestorrentvip.password");
        $url = \Config::get("sync.login_filmestorrentvip.url");
        $this->wpClientFilmesTorrentVip = new WordpressClient();
        $this->wpClientFilmesTorrentVip->setCredentials($url, $user, $password);
    }

    private function prepara_piratefilmes()
    {
        $user = \Config::get("sync.login_piratefilmes.user");
        $password = \Config::get("sync.login_piratefilmes.password");
        $url = \Config::get("sync.login_piratefilmes.url");
        $this->wpClientePirate = new WordpressClient();
        $this->wpClientePirate->setCredentials($url, $user, $password);
    }

    public function uploadImagem($site, $imagem)
    {
        switch ($site) {
            case Sites::FILMESVIATORRENT_INFO:
                $this->verificar_login_filmes_info();
                return $this->upload_imagem($this->wpClienteFilmesInfo, $imagem);
            case Sites::FILMESVIATORRENT_ORG:
                $this->verificar_login_filmes_org();
                return $this->upload_imagem($this->wpClienteFilmesOrg, $imagem);
            case Sites::PIRATEFILMES_NET:
                $this->verificar_login_filmes_pirate();
                return $this->upload_imagem($this->wpClientePirate, $imagem);
            case Sites::FILMESTORRENT_VIP:
                $this->verificar_login_filmesviatorrent_vip();
                return $this->upload_imagem($this->wpClientFilmesTorrentVip, $imagem);
            case Sites::JOGOS_TORRENT:
                $this->verificar_login_jogos_torrent();
                return $this->upload_imagem($this->wpJogosTorrent, $imagem);
            case Sites::SERIESONLINE_PRO:
                $this->verificar_login_seriesonline_pro();
                return $this->upload_imagem($this->wpSeriesOnlinePro,$imagem);
            default:
                return [];
        }
    }

    public function addPostPorSiteJogo($site, Jogo $jogo)
    {
        $this->verificar_login_jogos_torrent();
        return $this->addPostSiteJogo($this->wpJogosTorrent, $jogo);
    }

    public function addPostPorSiteSomenteSerie($site, Serie $serie){
        $this->verificar_login_seriesonline_pro();
        return $this->addPostSerieOnly($this->wpSeriesOnlinePro,$serie,$serie->getTituloPronto());
    }

    private function addPostSerieOnly(WordpressClient $wpCliente, Serie $serie, $title)
    {
        return $wpCliente->newPost($title, $serie->descricao, $serie->content);
    }

    public function addPostGenericoPorSite($site,$title,$descricao, $content,$data = null){
        $this->verificar_login_seriesonline_pro();
        if(!is_null($data)){
            $content['post_date'] = $this->wpSeriesOnlinePro->createXMLRPCDateTime($data);
        }
        return $this->addPostGenerico($this->wpSeriesOnlinePro,$title,$descricao, $content);
    }

    private function addPostGenerico(WordpressClient $wpCliente, $title,$descricao, $content){
        return $wpCliente->newPost($title, $descricao, $content);
    }

    public function addPostPorSiteEpisodio($site,Episodio $episodio){
        $this->verificar_login_seriesonline_pro();
        return $this->wpSeriesOnlinePro->newPost($episodio->getTituloPronto(),$episodio->descricao,$episodio->content);
    }

    public function addPostPorSite($site, Filme $filme, $imagem_destaque_id = null)
    {
        switch ($site) {
            case Sites::FILMESVIATORRENT_INFO:
                $this->verificar_login_filmes_info();
                return $this->addPostSite($this->wpClienteFilmesInfo, $filme, $filme->titulo_filmes_via_torrent_info,
                    $imagem_destaque_id);
            case Sites::FILMESVIATORRENT_ORG:
                $this->verificar_login_filmes_org();
                return $this->addPostSite($this->wpClienteFilmesOrg, $filme, $filme->titulo_filmes_via_torrent_org,
                    $imagem_destaque_id);
            case Sites::FILMESVIATORRENT_BIZ:
                $this->verificar_login_filmes_biz();
                return $this->addPostSiteBiz($this->wpClienteFilmesBiz, $filme);
            case Sites::PIRATEFILMES_NET:
                $this->verificar_login_filmes_pirate();
                return $this->addPostSite($this->wpClientePirate, $filme, $filme->titulo_piratefilmes,
                    $imagem_destaque_id);
            case Sites::TORRENT_VIP:
                $this->verificar_login_torrent_vip();
                return $this->addPostSiteTorrentVip($this->wpClientTorrentVip, $filme);
            case Sites::FILMESTORRENT_VIP:
                $this->verificar_login_filmesviatorrent_vip();
                return $this->addPostSite($this->wpClientFilmesTorrentVip, $filme, $filme->titulo_filmesvip,
                    $imagem_destaque_id);
            case Sites::KFILMES:
                $this->verificar_login_kfilmes();
                return $this->addPostKFilme($this->wpKfilmes, $filme);
            default:
                return "";
        }
    }

    public function editPostPorSite($site, $post_id, Filme $filme)
    {
        try{
            switch ($site) {
                case Sites::FILMESVIATORRENT_INFO:
                    $this->verificar_login_filmes_info();
                    return $this->editPost($this->wpClienteFilmesInfo, $post_id, $filme);
                case Sites::FILMESVIATORRENT_ORG:
                    $this->verificar_login_filmes_org();
                    return $this->editPost($this->wpClienteFilmesOrg, $post_id, $filme);
                case Sites::FILMESVIATORRENT_BIZ:
                    $this->verificar_login_filmes_biz();
                    return $this->editPostBiz($this->wpClienteFilmesBiz, $post_id, $filme);
                case Sites::PIRATEFILMES_NET:
                    $this->verificar_login_filmes_pirate();
                    return $this->editPost($this->wpClientePirate, $post_id, $filme);
                case Sites::TORRENT_VIP:
                    $this->verificar_login_torrent_vip();
                    return $this->editPostTorrentVIP($this->wpClientTorrentVip, $post_id, $filme);
                case Sites::FILMESTORRENT_VIP:
                    $this->verificar_login_filmesviatorrent_vip();
                    return $this->editPost($this->wpClientFilmesTorrentVip, $post_id, $filme);
                default:
                    return "";
            }
        }catch (\Throwable $ex){
            \Log::info("Erro ao atualizar a postagem ID: $post_id, no site: $site, sobre o filme:".$filme->titulo." Erro: ".$ex->getMessage());
            return "";
        }

    }

    private function get_post(WordpressClient &$wpCliente, $post_id)
    {
        return $wpCliente->getPost($post_id);
    }

    public function get_post_por_site($post_id, $site)
    {
        switch ($site) {
            case Sites::FILMESVIATORRENT_INFO:
                $this->verificar_login_filmes_info();
                return $this->get_post($this->wpClienteFilmesInfo, $post_id);
            case Sites::FILMESVIATORRENT_ORG:
                $this->verificar_login_filmes_org();
                return $this->get_post($this->wpClienteFilmesOrg, $post_id);
            case Sites::FILMESVIATORRENT_BIZ:
                $this->verificar_login_filmes_biz();
                return $this->get_post($this->wpClienteFilmesBiz, $post_id);
            case Sites::PIRATEFILMES_NET:
                $this->verificar_login_filmes_pirate();
                return $this->get_post($this->wpClientePirate, $post_id);
            case Sites::FILMESTORRENT_VIP:
                $this->verificar_login_filmesviatorrent_vip();
                return $this->get_post($this->wpClientFilmesTorrentVip, $post_id);
            case Sites::TORRENT_VIP:
                $this->verificar_login_torrent_vip();
                return $this->get_post($this->wpClientTorrentVip, $post_id);
            case Sites::JOGOS_TORRENT:
                $this->verificar_login_jogos_torrent();
                return $this->get_post($this->wpJogosTorrent, $post_id);
            default:
                return "";
        }
    }

    private function upload_imagem(WordpressClient &$wpClienteSite, $imagem)
    {
        $path_imagem = "../public/img/baixadas/$imagem";
        $mime = 'image/png';
        $data = file_get_contents($path_imagem);
        return $wpClienteSite->uploadFile($imagem, $mime, $data, true);
    }

    private function verificar_login_filmes_info()
    {
        if (!$this->autenticado_filmes_info) {
            $this->prepara_filmesviatorrent_info();
            $this->autenticado_filmes_info = true;
        }
    }

    private function verificar_login_jogos_torrent()
    {
        if (!$this->autenticado_jogos_torrent) {
            $this->prepara_jogos_torrent();
            $this->autenticado_jogos_torrent = true;
        }
    }

    private function verificar_login_seriesonline_pro()
    {
        if (!$this->autenticado_seriesonline_pro) {
            $this->prepara_seriesonlinepro();
            $this->autenticado_seriesonline_pro = true;
        }
    }

    private function verificar_login_filmes_org()
    {
        if (!$this->autenticado_filmes_org) {
            $this->prepara_filmesviatorrent_org();
            $this->autenticado_filmes_org = true;
        }
    }

    private function verificar_login_filmes_pirate()
    {
        if (!$this->autenticado_pirate) {
            $this->prepara_piratefilmes();
            $this->autenticado_pirate = true;
        }
    }

    private function verificar_login_filmes_biz()
    {
        if (!$this->autenticado_filmes_biz) {
            $this->prepara_filmesviatorrent_biz();
            $this->autenticado_filmes_biz = true;
        }
    }

    private function verificar_login_torrent_vip()
    {
        if (!$this->autenticado_torrent_vip) {
            $this->prepara_torrent_vip();
            $this->autenticado_torrent_vip = true;
        }
    }

    private function verificar_login_site($site)
    {
        switch ($site) {
            case Sites::FILMESVIATORRENT_INFO:
                $this->verificar_login_filmes_info();
                break;
            case Sites::FILMESVIATORRENT_ORG:
                $this->verificar_login_filmes_org();
                break;
            case Sites::FILMESVIATORRENT_BIZ:
                $this->verificar_login_filmes_biz();
                break;
            case Sites::PIRATEFILMES_NET:
                $this->verificar_login_filmes_pirate();
                break;
        }
    }

    private function editPost(WordpressClient $wpCliente, $post_id, Filme $filme)
    {
        $content = [
            "post_content" => $filme->content,
            'post_date' => $wpCliente->createXMLRPCDateTime(Carbon::now()->toDateTime())
        ];
        return $wpCliente->editPost($post_id, $content);

    }

    public function editPostSeriePorSite($site,$post_id,Serie $serie){

        $content = $serie->content;
        $content['post_date'] = $this->wpSeriesOnlinePro->createXMLRPCDateTime(Carbon::now()->toDateTime());
        return $this->wpSeriesOnlinePro->editPost($post_id, $content);
    }

    public function editPostGenerico($site,$post_id,$content){
        $this->verificar_login_seriesonline_pro();
        $content['post_date'] = $this->wpSeriesOnlinePro->createXMLRPCDateTime(Carbon::now()->toDateTime());
        return $this->wpSeriesOnlinePro->editPost($post_id, $content);
    }

    private function editPostBiz(WordpressClient $wpCliente, $post_id, Filme $filme)
    {
        $content = $filme->custom_content;
        $content['post_date'] = $wpCliente->createXMLRPCDateTime(Carbon::now()->toDateTime());
        return $wpCliente->editPost($post_id, $content);

    }

    private function editPostTorrentVIP(WordpressClient $wpCliente, $post_id, Filme $filme)
    {
        $content = $filme->custom_content;
        $content['post_date'] = $wpCliente->createXMLRPCDateTime(Carbon::now()->toDateTime());
        return $wpCliente->editPost($post_id, $content);

    }

    private function addPostSite(WordpressClient $wpCliente, Filme $filme, $title, $imagem_destaque_id = null)
    {
        if (is_null($imagem_destaque_id)) {
            $content = ["terms_names" => ["category" => $filme->categorias_separadas], "comment_status" => "open"];
        } else {
            $content = [
                "terms_names" => ["category" => $filme->categorias_separadas],
                "post_thumbnail" => (int) $imagem_destaque_id, "comment_status" => "open"
            ];
        }
        return $wpCliente->newPost($title, $filme->content, $content);
    }

    private function addPostSiteJogo(WordpressClient $wpClient, Jogo $jogo)
    {
        $jogo->custom_content["comment_status"] = "open";
        $jogo->custom_content["terms_names"] = ["category" => $jogo->generos];
        return $wpClient->newPost($jogo->titulo, $jogo->content, $jogo->custom_content);
    }

    private function addPostSiteBiz(WordpressClient $wpCliente, Filme $filme)
    {
        if(empty($filme->movie_name)){
            $titulo = $filme->theMovieDB->titulo;
        }else{
            $titulo = $filme->movie_name;
        }
        if ($filme->is_serie) {
            $titulo .= " ".$filme->serie_temporada."ª Temporada";
        }
        return $wpCliente->newPost($titulo, $filme->content,
            $filme->custom_content);
    }

    private function addPostKFilme(WordpressClient $wpCliente, Filme $filme)
    {
        return $wpCliente->newPost($filme->titulo_kfilme, $filme->content,
            $filme->custom_content);
    }

    private function addPostSiteTorrentVip(WordpressClient $wpCliente, Filme $filme)
    {

        $titulo = $filme->theMovieDB->titulo;
        if ($filme->is_serie) {
            $titulo .= " ".$filme->serie_temporada."ª Temporada";
        }
        return $wpCliente->newPost($titulo, $filme->content,
            $filme->custom_content);
    }

    public function getPostGenericoPorSite($site,$post_id){
        $this->verificar_login_seriesonline_pro();
        return $this->wpSeriesOnlinePro->getPost($post_id);
    }

    public function atualizar_post_biz($post_id)
    {
        try {
            return $this->wpClienteFilmesBiz->editPost($post_id, []);
        } catch (\Throwable $ex) {
            \Log::info("Erro ao atualizar postagem no biz, post ID: $post_id, Erro".$ex->getMessage());
        }
    }

    public function excluir_post_biz($post_id)
    {
        try {
            $this->verificar_login_filmes_biz();
            $this->wpClienteFilmesBiz->deletePost($post_id);
        } catch (\Throwable $ex) {
            \Log::info("Erro ao excluir postagem no biz, post ID: $post_id, Erro: ".$ex->getMessage());
        }

    }

    private function prepara_jogos_torrent()
    {
        $user = \Config::get("sync.login_jogostorrent.user");
        $password = \Config::get("sync.login_jogostorrent.password");
        $url = \Config::get("sync.login_jogostorrent.url");
        $this->wpJogosTorrent = new WordpressClient();
        $this->wpJogosTorrent->setCredentials($url, $user, $password);
    }

    private function verificar_login_filmesviatorrent_vip()
    {
        if (!$this->autenticado_filmesviatorrent_vip) {
            $this->prepara_filmesviatorrent_vip();
            $this->autenticado_filmesviatorrent_vip = true;
        }
    }
    private function verificar_login_kfilmes()
    {
        if (!$this->autenticado_kfilmes) {
            $this->prepara_kfilmes();
            $this->autenticado_kfilmes = true;
        }
    }


}
