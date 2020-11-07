<?php


    namespace App\Models;


    use App\Utils\FuncoesUteis;
    use GuzzleHttp\Client;
    use GuzzleHttp\Cookie\CookieJar;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use voku\helper\HtmlDomParser;

    class CursoSEO
    {
        private $link_curso;
        public $titulo_curso;
        private $secoes;
        private $client;
        private $cookie;
        public $log;

        /**
         * CursoSEO constructor.
         * @param $link_curso
         * @param $cookie_name
         * @param $cookie_value
         */
        public function __construct($link_curso, $cookie_name, $cookie_value)
        {
            $this->link_curso = $link_curso;
            $this->titulo_curso = "";
            $this->secoes = [];
            $headers = ['Referer' => 'https://vip.seodeverdade.pro'];
            $this->cookie = CookieJar::fromArray([
                $cookie_name => $cookie_value
            ], 'vip.seodeverdade.pro');
            $this->client = new Client([
                "headers" => $headers
            ]);
        }

        public function carregar_paginas()
        {
            $response = $this->getReponsePage($this->link_curso);
            if ($response != false) {
                $dom = HtmlDomParser::str_get_html($response);
                $titulo = $dom->findOneOrFalse("div.stm_lms_lesson_header__center > h5 > a");
                if ($titulo != false) {
                    $this->titulo_curso = $titulo->text();
                    $titulo_secao = $dom->findOneOrFalse("div.stm-lms-course__lesson-content__top > h1");
                    $descricao = $dom->findOneOrFalse("div.stm-lms-course__lesson-content");
                    if ($titulo_secao != false && $descricao != false) {
                        $link_player = $dom->findOneOrFalse("div.stm-lms-course__lesson-content > p > iframe");
                        if ($link_player != false) {
                            $link_player_vimeo = $link_player->getAttribute("src");
                            if (Str::contains($link_player_vimeo, "vimeo")) {
                                $link_download = $this->getLinkDownload($link_player_vimeo);
                                if ($link_download != false) {
                                    $this->secoes[] = ["titulo" => FuncoesUteis::limpar_caracteres_especiais($titulo_secao->text(), false), "descricao" => $descricao->html(), "link" => $link_download];
                                }
                            } else {
                                $this->log .= "O link encontrado não é um vimeo" . $titulo_secao->text() . " \n\n\n";
                            }
                            $proximo_link = $dom->findOneOrFalse("div.stm-lms-lesson_navigation_next > a");
                            if ($proximo_link != false) {
                                $link_proximo = $proximo_link->getAttribute("href");
                                while (true) {
                                    $link_proximo = $this->preparaSecao($link_proximo);
                                    if ($link_proximo == false) {
                                        break;
                                    }
                                }
                            }
                        } else {
                            $this->log .= "Não foi encontrado o link do player em: " . $this->link_curso . " \n\n\n";
                        }
                    } else {
                        $this->log .= "Não foi possível pegar o titulo da seção ou descrição em: " . $this->link_curso . " \n\n\n";
                    }
                } else {
                    $this->log .= "Não foi possível pegar o titulo do curso em: " . $this->link_curso . " \n\n\n";
                }
            }
        }

        private function saveArquivos($path, $titulo, $descricao, $index)
        {
            try {
                Storage::put("cursos\\" . $this->titulo_curso . "\\" . $index . " - " . $titulo . ".html", $descricao);
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve um erro ao salvar arquivo: " . $titulo . " \n\n\n";
            }
        }

        public function preparaJsonLinksDownload()
        {
            try {
                $links_download = [];
                $count = 1;
                $path = public_path("cursos/" . $this->titulo_curso);
                \File::isDirectory($path) or \File::makeDirectory($path);
                foreach ($this->secoes as $secoe) {
                    $titulo = $secoe["titulo"];
                    $descricao = $secoe["descricao"];
                    $link = $secoe["link"];
                    $links_download[] = ["nome" => $titulo, "link" => $link, "episodio" => "" . $count, "temporada" => 1, "audio" => "DUBLADO"];
                    $this->saveArquivos($path, $titulo, $descricao, $count);
                    $count++;
                }
                return json_encode($links_download);
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return "";
            }
        }

        private function getLinkDownload($link_player)
        {
            try {
                $response = $this->client->get($link_player . "/config");
                $resultado = $response->getBody()->getContents();
                $result = json_decode($resultado);
                $qualidades = [1080, 720, 540, 360];
                $link_pronto = "";
                foreach ($qualidades as $qualidade) {
                    foreach ($result->request->files->progressive as $link) {
                        if ($link->height >= $qualidade) {
                            $link_pronto = $link->url;
                            break 2;
                        }
                    }
                }
                return $link_pronto;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Não foi possível pegar o link de download do player em" . $this->link_curso . " erro: " . $ex->getMessage() . " \n\n\n";
                return false;
            }
        }

        private function preparaSecao($url)
        {
            try {
                $response = $this->getReponsePage($url);
                if ($response != false) {
                    $dom = HtmlDomParser::str_get_html($response);
                    $titulo_secao = $dom->findOneOrFalse("div.stm-lms-course__lesson-content__top > h1");
                    $descricao = $dom->findOneOrFalse("div.stm-lms-course__lesson-content");
                    if ($titulo_secao != false && $descricao != false) {
                        $link_player = $dom->findOneOrFalse("div.stm-lms-course__lesson-content > p > iframe");
                        if ($link_player != false) {
                            $link_vimeo = $link_player->getAttribute("src");
                            if (Str::contains($link_vimeo, "vimeo")) {
                                $link_download = $this->getLinkDownload($link_vimeo);
                                if ($link_download != false) {
                                    $this->secoes[] = ["titulo" => FuncoesUteis::limpar_caracteres_especiais($titulo_secao->text(), false), "descricao" => $descricao->html(), "link" => $link_download];
                                }
                                $proximo_link = $dom->findOneOrFalse("div.stm-lms-lesson_navigation_next > a");
                                if ($proximo_link != false) {
                                    return $proximo_link->getAttribute("href");
                                } else {
                                    return false;
                                }
                            } else {
                                $this->log .= "O link encontrado não é um vimeo" . $titulo_secao->text() . " \n\n\n";
                                return true;
                            }
                        } else {
                            $this->log .= "Não foi encontrado o link do player em: " . $this->link_curso . " \n\n\n";
                            return true;
                        }
                    } else {
                        $this->log .= "Não foi possível pegar o titulo da seção ou descrição em: " . $this->link_curso . " \n\n\n";
                        return true;
                    }
                } else {
                    return true;
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve um erro em: " . $this->link_curso . " erro: " . $ex->getMessage() . " \n\n\n";
            }
            return false;
        }

        private function getReponsePage($url)
        {
            try {
                $response = $this->client->request("GET", $url, [
                    "cookies" => $this->cookie
                ]);
                return $response->getBody()->getContents();
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= "Houve um erro ao acessar página! em: " . $url . " erro: " . $ex->getMessage() . "\n\n\n";
                return false;
            }
        }


    }
