<?php


    namespace App\Models\filmes;


    use App\Enums\AudioFilme;
    use App\Enums\Sites;
    use App\Models\filmes\download\LinksDownloads;
    use App\Models\filmes\Utils\FuncoesUteisFilme;
    use App\Models\filmes\Utils\GeraCodeFilme;
    use App\Models\Imagens;
    use App\Models\IMDB;
    use App\Utils\FuncoesUteis;
    use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

    /**
     * Class Filme
     * @property LinksDownloads[] $links_downloads
     * @property TheMovieDB $theMovieDB
     * @property IMDB $imdb;
     * @package App\Models\filmes
     */
    class Filme extends FuncoesUteisFilme
    {
        public $titulo_original, $titulo_traduzido, $formato, $qualidade, $audio, $legenda, $generos, $tamanho, $qualidade_audio, $qualidade_video, $ano_lancamento, $trailer_1, $trailer_2, $trailer_3, $trailer_4, $trailer_5, $trailer_6, $trailer_7, $duracao, $sinopse, $audioFilme, $qualidade_original, $links_downloads, $id_the_movie, $url_imagem_the_movie, $titulo, $nota_imdb, $content, $img_site, $titulo_filmes_via_torrent_info, $titulo_filmes_via_torrent_org, $titulo_piratefilmes, $titulo_filmesvip, $categorias_separadas, $img_url_upload, $theMovieDB, $imdb, $custom_content, $is_serie, $serie_anotacao, $filme_ou_serie_texto, $filme_ou_serie, $serie_temporada, $links_download_serie, $is_cinema, $titulo_kfilme;
        public $log = "";
        public $movie_name = "";

        public function carregar_dados()
        {

        }

        public function gera_code_por_site($site)
        {
            $code = new GeraCodeFilme($this);
            switch ($site) {
                case Sites::FILMESVIATORRENT_INFO:
                    $code->gerar_code_filmesviatorrent_info();
                    break;
                case Sites::FILMESVIATORRENT_ORG:
                    $code->gerar_code_filmesviatorrent_org();
                    break;
                case Sites::PIRATEFILMES_NET:
                    $code->gerar_code_piratefilmes();
                    break;
                case Sites::FILMESVIATORRENT_BIZ:
                    $code->gerarCode_custom_content_biz();
                    break;
                case Sites::TORRENT_VIP:
                    $code->gerar_code_torrent_vip();
                    break;
                case Sites::FILMESTORRENT_VIP:
                    $code->gerar_code_filmesviatorrent_vip();
                    break;
                case Sites::KFILMES:
                    $code->gerarCodeKFilmes();
                    break;
            }
        }

        public function preparar_imagens_por_site($site)
        {
            switch ($site) {
                case Sites::FILMESVIATORRENT_INFO:
                    $this->colocar_logo_imagem($this->preparar_imagem_filmes_info(), $site);
                    break;
                case Sites::FILMESVIATORRENT_ORG:
                    $this->colocar_logo_imagem($this->preparar_imagem_filmes_org(), $site);
                    break;
                case Sites::PIRATEFILMES_NET:
                    $this->colocar_logo_imagem($this->preparar_imagem_pirate(), $site);
                    break;
                case Sites::FILMESTORRENT_VIP:
                    $this->colocar_logo_filmestorrentvip();
                    break;
            }
        }

        private function colocar_logo_filmestorrentvip()
        {
            $nome = FuncoesUteis::limpar_caracteres_especiais($this->titulo);
            $nome_pronto = $nome . "-filmestorrent-vip.png";
            Imagens::colocar_logo_na_imagem(Sites::FILMESTORRENT_VIP, public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome_pronto"),
                public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome_pronto"));
            $this->img_site = $nome_pronto;
            ImageOptimizer::optimize("img/baixadas/" . $nome_pronto);
        }

        private function colocar_logo_imagem($nome, $site)
        {
            Imagens::colocar_logo_na_imagem($site, public_path("img" . DIRECTORY_SEPARATOR . "temp2.png"),
                public_path("img" . DIRECTORY_SEPARATOR . "baixadas" . DIRECTORY_SEPARATOR . "$nome"));
            ImageOptimizer::optimize("img/baixadas/" . $nome);
        }

        private function preparar_imagem_filmes_info()
        {
            return $this->preparar_imagens_filmes("-torrent-info.jpeg");
        }

        private function preparar_imagem_filmesviatorrent_vip()
        {
            return $this->preparar_imagens_filmes("-filmestorrent-vip.png");
        }

        private function preparar_imagem_filmes_org()
        {
            return $this->preparar_imagens_filmes("-torrent-org.jpeg");
        }

        private function preparar_imagem_pirate()
        {
            return $this->preparar_imagens_filmes("-piratefilmes.jpeg");
        }

        private function preparar_imagens_filmes($extensao_nome)
        {
            $nome = FuncoesUteis::limpar_caracteres_especiais($this->titulo);
            $nome_pronto = $nome . $extensao_nome;
            $this->img_site = $nome_pronto;
            return $nome_pronto;
        }

        public function get_imagem_por_site($site)
        {
            return $this->img_site;
        }

        public function is_legendado()
        {
            return $this->audioFilme == AudioFilme::LEGENDADO;
        }

    }
