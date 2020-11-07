<?php


    namespace App\Models;


    use App\Enums\Sites;
    use Illuminate\Support\Str;
    use Intervention\Image\ImageManager;
    use Symfony\Component\Process\Process;

    class Imagens
    {
        public static function colocar_logo_na_imagem($site, $imagem, $nova_imagem)
        {
            switch ($site) {
                case Sites::FILMESVIATORRENT_INFO:
                    return self::colocar_logo(public_path("img" . DIRECTORY_SEPARATOR . "padrao.png"), $imagem, $nova_imagem, 525, 610);
                case Sites::FILMESVIATORRENT_ORG:
                    return self::colocar_logo(public_path("img" . DIRECTORY_SEPARATOR . "padrao-torrentOrg.png"), $imagem, $nova_imagem, 345, 410);
                case Sites::PIRATEFILMES_NET:
                    return self::colocar_logo(public_path("img" . DIRECTORY_SEPARATOR . "padrao-piratefilmes.png"), $imagem, $nova_imagem, 220, 283, "left", 10);
                case Sites::FILMESTORRENT_VIP:
                    return self::colocar_logo(public_path("img" . DIRECTORY_SEPARATOR . "padrao-filmesvip.png"), $imagem, $nova_imagem, 352, 407);
                case Sites::SERIESONLINE_PRO:
                    return self::colocar_logo(public_path("img" . DIRECTORY_SEPARATOR . "padrao-seriesonlinepro.png"), $imagem, $nova_imagem, 454, 254);
                default:
                    return [];
            }
        }

        public static function colocar_logo_anime($imagem, $nova_imagem, $is_capa = false, $use_themovie = false)
        {
            $logo_padrao = $is_capa ? "img" . DIRECTORY_SEPARATOR . "padrao-anime-capa.png" : "img" . DIRECTORY_SEPARATOR . "padrao-anime.png";
            $width = $is_capa ? 200 : 320;
            $heigth = $is_capa ? 282 : 209;
            if ($use_themovie) {
                return self::colocar_logo(public_path($logo_padrao), $imagem, $nova_imagem, 200, 282);
            }
            return self::colocar_logo(public_path($logo_padrao), $imagem, $nova_imagem, $width, $heigth);
        }

        private static function colocar_logo($imagem_logo, $imagem, $nova_imagem, $width, $heigth, $position = "center", $x = 0)
        {
            $img = new ImageManager(array('driver' => 'gd'));
            $img_logo = $img->make($imagem_logo);
            $img = $img->make($imagem);
            $img->resize($width, $heigth);
            $img->save();
            $img_logo->insert($imagem, $position, $x);
            $img_logo->insert($imagem_logo, "center")->save($nova_imagem);
            return $nova_imagem;
        }

        public static function colocar_logo_somente_serie($site, $imagem, $nova_imagem, $is_capa)
        {
            $logo_padrao = $is_capa ? "img" . DIRECTORY_SEPARATOR . "padrao-seriesonlinepro-capav2.png" : "img" . DIRECTORY_SEPARATOR . "padrao-seriesonlinepro.png";
            if ($is_capa) {
                return self::colocar_logo(public_path($logo_padrao), $imagem, $nova_imagem, 250, 375);
            } else {
                return self::colocar_logo_serie(public_path($logo_padrao), $imagem, $nova_imagem);
            }

        }

        private static function colocar_logo_serie($imagem_logo, $imagem, $nova_imagem)
        {
            $img = new ImageManager(array('driver' => 'gd'));
            $img_logo = $img->make($imagem_logo);
            $img_logo->insert($imagem, "center", 0);
            $img_logo->insert($imagem_logo, "center")->save($nova_imagem);
            return $nova_imagem;
        }

        public static function copy($image, $imagecopy)
        {
            return \File::copy($image, $imagecopy);
        }

        /**
         * return true se deu certo, e false se não.
         * @param string $time
         * @param string $imagem_url
         * @param string $link_download
         * @param string $resolucao
         * @return mixed
         */
        public static function tirar_print($time = "00:10:00", $imagem_url = "img/temp.png", $link_download = "",$resolucao = "454:254")
        {
            $script = 'ffmpeg -ss ' . $time . ' -y -i "' . $link_download . '" -f image2 -vf scale='.$resolucao.' -vframes 1 "' . $imagem_url . '"';
            $p = new Process($script);
            $p->run();
            $resultado = $p->getErrorOutput()." ".$p->getOutput();
            if(Str::contains($resultado, "Output file is empty, nothing was encoded") || Str::contains($resultado, "error 404 Not Found") || Str::contains($resultado, "Error while processing the decoded data")){
                return false;
            }
            return true;
        }
    }
