<?php


namespace App\Utils;


use App\Enums\Sites;
use App\Models\filmes\Filme;
use App\Models\filmes\Utils\FuncoesUteisFilme;
use Arcanedev\LogViewer\Entities\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use mysql_xdevapi\Exception;
use voku\helper\HtmlDomParser;

class FuncoesUteis
{
    /**
     * @param $regex
     * @param $str
     * @return mixed
     */
    public static function useRegex($regex, $str)
    {
        preg_match($regex, $str, $resultado);
        return $resultado;
    }

    public static function get_web_page($url)
    {
        $before = microtime(true);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER => true,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS => 10,     // stop after 10 redirects
            CURLOPT_ENCODING => "",     // handle compressed
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36", // name of client
            CURLOPT_AUTOREFERER => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT => 120,    // time-out on response
            CURLOPT_NOBODY => false,
            CURLOPT_REFERER => "https://www.google.com.br/"
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: __cfduid=d12b6ff746a47afb701d20a2fe63113b11584984729; SERVERID68970=264081; cdn_12=1; PHPSESSID=c866891bfb35dea15f1293f1bacefd4a; _ga=GA1.2.1014448095.1584984731; _gid=GA1.2.1219776068.1584984731; packet2=TlNObmJQNVF4R3V2azQ5WVRZZ0ZVTFZLT2E3NjM4OFRiZE9qb0oxcVhyamFsVXJHQ0VRczNzT3JSTmhtWnVtQQ%3D%3D"));

        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    public static function ajusta_download_texto($texto, $qualidade, $qualidade_link)
    {
        $t = str_replace("Versão", "", $texto);
        $t = str_replace($qualidade, "", $t);
        $t = str_replace($qualidade_link, "", $t);
        return $t;
    }

    /**
     * @param $url
     * @param $imagem
     * @return bool|int
     */
    public static function baixar_imagem($url, $imagem)
    {
        $contents = file_get_contents(trim($url));
        Storage::put($imagem, $contents);
        return true;
    }

    public static function colocar_logo_na_imagem($site, $imagem, $nova_imagem)
    {
        switch ($site) {
            case Sites::FILMESVIATORRENT_INFO:
                return self::colocar_logo(public_path("img" . DIRECTORY_SEPARATOR . "padrao.png"), $imagem, $nova_imagem, 525, 610);
        }
    }

    private static function colocar_logo($imagem_logo, $imagem, $nova_imagem, $width, $heigth)
    {
        $img = new ImageManager(array('driver' => 'gd'));
        $img_logo = $img->make($imagem_logo);
        $img = $img->make($imagem);
        $img->resize($width, $heigth);
        $img->save();
        $img_logo->insert($imagem, "center");
        $img_logo->insert($imagem_logo, "center")->save($nova_imagem);
        return $nova_imagem;
    }

    public static function limpar_caracteres_especiais($texto, $remove_space = true)
    {
        $t = self::remover_ascentos($texto);
        $t = str_replace(":", "", $t);
        $t = str_replace("(", "", $t);
        $t = str_replace(")", "", $t);
        $t = str_replace("&", "E", $t);
        $t = str_replace("?", "", $t);
        $t = str_replace("!", "", $t);
        $t = str_replace(",", "", $t);
        $t = str_replace("/", "", $t);
        $t = str_replace(";", "", $t);
        $t = str_replace("&#8211;", "", $t);
        $t = str_replace("&#8217;", "", $t);
        $t = str_replace("E#8217;", "", $t);
        $t = str_replace("E#8211", "", $t);
        $t = str_replace("amp;", "", $t);
        $t = str_replace("\"", "", $t);
        $t = str_replace("'", "", $t);
        if ($remove_space) {
            $t = preg_replace('/\s+/', '-', $t);
        }
        return $t;
    }

    public static function limpar_caracteres_especiais_bludv($texto)
    {
        $t = self::remover_ascentos($texto);
        $t = str_replace(":", "", $t);
        $t = str_replace("(", "", $t);
        $t = str_replace(")", "", $t);
        $t = str_replace("&", "E", $t);
        $t = str_replace("?", "", $t);
        $t = str_replace("!", "", $t);
        $t = str_replace(",", "", $t);
        $t = str_replace("/", "", $t);
        $t = str_replace("&#8211;", "", $t);
        $t = str_replace("&#8217;", "", $t);
        $t = str_replace("E#8217;", "", $t);
        $t = str_replace("amp;", "", $t);
        $t = str_replace("&#038;", "e", $t);
        $t = str_replace("&#8211;", "", $t);
        $t = str_replace("torrent", "", $t);
        return $t;
    }

    public static function remover_ascentos($texto)
    {
        return preg_replace(array(
            "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/",
            "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"
        ), explode(" ", "a A e E i I o O u U n N"), $texto);
    }

    public static function pegar_titulo_filme($link)
    {
        try {
            $dom = HtmlDomParser::file_get_html($link);
            if (Str::contains($link, "bkseries")) {
                $nome = $dom->findOne("span.last-bread")->text();
            } else {
                if (Str::contains($link, "comando")) {
                    $titulo_original = "b:contains('Original')";
                    $titulo_traduzido = "b:contains('Traduzido')";
                } else {
                    $titulo_original = "b:contains('Original')";
                    $titulo_traduzido = "b:contains('Traduzido')";
                }
                $nome = trim(explode("/", FuncoesUteisFilme::pegar_dados_e_verificar_static($dom, [$titulo_original, "strong:contains('Original')", "b:contains('Título')", "b:contains('Titulo')"]))[0]);
                if (empty($nome) || strlen($nome) <= 3) {
                    $nome = FuncoesUteisFilme::pegar_dados_e_verificar_static($dom, [$titulo_traduzido, "strong:contains('Traduzido')"]);
                }
                $nome = FuncoesUteis::limpar_caracteres_especiais_bludv($nome);
            }
            $nome = preg_replace('/(.ª Temporada.*)/m', "", $nome);
            return $nome;
        } catch (\Exception $ex) {
            \Log::error($ex);
            return "";
        }
    }

    public static function identificar_temporada_serie($titulo)
    {
        try {
            preg_match('/(..ª.*)Temporada|(..°.*)Temporada/', $titulo, $saida);
            $resultado = str_replace("ª", "", $saida);
            $resultado = str_replace("-", "", $resultado);
            $resultado = str_replace("°", "", $resultado);
            return trim(empty($resultado[1]) ? $resultado[2] : $resultado[1]);
        } catch (\Exception $ex) {
            \Log::error($ex);
            return "1";
        }
    }

    public static function ajustaFormato($formato)
    {
        return trim(self::multipleReplace([':'], '', $formato));
    }

    public static function procurar_ID_postagem($nome)
    {
        $dados = [];
        foreach (Sites::get_movie_sites() as $site) {
            try {
                $key = "sync.sites_url." . $site;
                $url = \Config::get($key) . "/?s=" . urlencode($nome);
                $dom = HtmlDomParser::file_get_html($url);
                if ($site == Sites::FILMESTORRENT_VIP) {
                    $dom = HtmlDomParser::file_get_html($dom->findOne("h2.postContainerTitulo > a")->getAttribute("href"));
                    $link_post_id = $dom->findOne("link[rel*='shortlink']")->getAttribute("href");
                    preg_match('/[0-9]*[0-9]/', $link_post_id, $post_id);
                    $dados[$site] = ["titulo" => $dom->findOne("div.postContainer > h1 > a")->text(), "id" => $post_id[0]];
                } else if ($site == Sites::FILMESVIATORRENT_BIZ) {
                    $texto = $dom->findOne("div.TpRwCont > main > section > ul > li > article > a")->text();
                    $dom = HtmlDomParser::file_get_html($dom->findOne("div.TpRwCont > main > section > ul > li > article > a")->getAttribute("href"));
                    $link_post_id = $dom->findOne("link[rel*='shortlink']")->getAttribute("href");
                    preg_match('/[0-9]*[0-9]/', $link_post_id, $post_id);
                    $dados[$site] = ["titulo" => $texto, "id" => $post_id[0]];
                } else {
                    $texto = $dom->findOne(".post-title > a")->text();
                    $dom = HtmlDomParser::file_get_html($dom->findOne(".post-title > a")->getAttribute("href"));
                    $post_id = $dom->findOne("input[name='comment_post_ID']")->getAttribute("value");
                    $dados[$site] = ["titulo" => $texto, "id" => $post_id];
                }
            } catch (\Exception $ex) {
                \Log::error($ex);
                $dados[$site] = ["titulo" => "Não encontrado", "id" => ""];
            }
        }
        return $dados;
    }

    /**
     * retorna true se for um link normal, false para um link do google
     * @param $link
     * @return bool
     */
    public static function identificar_links_normais($link)
    {
        $links_normais = ["video", "letsupload"];
        foreach ($links_normais as $url) {
            if (strpos($link, $url) !== FALSE) {
                return true;
            }
        }
        return false;
    }

    /**
     * retorna true se encontrar um link embed
     * @param $link
     * @return bool
     */
    public static function identificar_links_embed($link)
    {
        $link_embed = ["letsupload"];
        foreach ($link_embed as $url) {
            if (strpos($link, $url) !== FALSE) {
                return true;
            }
        }
        return false;
    }

    public static function pegarIDLinkIMDB($link)
    {
        $re = '/title\/(.*)\/|title\/(.*)/m';
        preg_match_all($re, $link, $matches, PREG_SET_ORDER, 0);
        return empty($matches[0][1]) ? $matches[0][2] : $matches[0][1];
    }

    public static function remover_palavras_animes($nome)
    {
        $remove = ["(TV)", "'", "-", "?", "amp;", ";"];
        $result = $nome;
        foreach ($remove as $r) {
            $result = str_replace($r, "", $result);
        }
        return trim($result);
    }

    public static function multipleReplace(array $search, $replace_for, $data)
    {
        $replace = $data;
        foreach ($search as $d) {
            $replace = str_replace($d, $replace_for, $replace);
        }
        return $replace;
    }
}
