<?php


    namespace App\Models\filmes\Utils;


    use App\Enums\AppDadosEnum;
    use App\Enums\QualidadeLinkFilme;
    use App\Models\filmes\Filme;
    use App\Utils\FuncoesUteis;

    class GeraCodeFilme
    {
        private $codeFilmesViaTorrent_info, $codeFilmesViaTorrent_org, $codePirate, $code_torrent_vip, $code_filmesviatorrent_vip;
        private $filme;

        /**
         * GeraCodeFilme constructor.
         * @param $filme
         */
        public function __construct(Filme $filme)
        {
            $this->filme = $filme;
        }


        private function trocar_dados($dados)
        {
            $d = str_replace(AppDadosEnum::FILME_NOME_TRADUZIDO, $this->filme->titulo_traduzido, $dados);
            $d = str_replace(AppDadosEnum::FILME_NOME_ORIGINAL, $this->filme->titulo_original, $d);
            $d = str_replace(AppDadosEnum::FILME_FORMATO, $this->filme->formato, $d);
            $d = str_replace(AppDadosEnum::FILME_QUALIDADE, $this->filme->qualidade, $d);
            $d = str_replace(AppDadosEnum::FILME_AUDIO, $this->filme->audio, $d);
            $d = str_replace(AppDadosEnum::FILME_LEGENDA, $this->filme->legenda, $d);
            $d = str_replace(AppDadosEnum::FILME_GENERO, $this->filme->generos, $d);
            $d = str_replace(AppDadosEnum::FILME_TAMANHO, $this->filme->tamanho, $d);
            $d = str_replace(AppDadosEnum::FILME_QUALIDADE_AUDIO, $this->filme->qualidade_audio, $d);
            $d = str_replace(AppDadosEnum::FILME_QUALIDADE_VIDEO, $this->filme->qualidade_video, $d);
            $d = str_replace(AppDadosEnum::FILME_ANO_LANCAMENTO, $this->filme->ano_lancamento, $d);
            $d = str_replace(AppDadosEnum::FILME_DURACAO, $this->filme->duracao, $d);
            $d = str_replace(AppDadosEnum::FILME_IMDB_NOTA, $this->filme->nota_imdb, $d);
            $d = str_replace(AppDadosEnum::FILME_SINOPSE, $this->filme->sinopse, $d);
            $d = str_replace(AppDadosEnum::FILME_TRAILER_YOUTUBE, $this->filme->trailer_1, $d);
            $d = str_replace(AppDadosEnum::FILME_QUALIDADE_ORIGINAL, $this->filme->qualidade_original, $d);
            $d = str_replace(AppDadosEnum::FILME_IMAGEM_UPLOAD, $this->filme->img_url_upload, $d);
            $d = str_replace(AppDadosEnum::FILME_TRAILER_YOUTUBE_2, $this->filme->trailer_2, $d);
            $d = str_replace(AppDadosEnum::FILME_TRAILER_YOUTUBE_3, $this->filme->trailer_3, $d);
            $d = str_replace(AppDadosEnum::FILME_TRAILER_YOUTUBE_4, $this->filme->trailer_4, $d);
            $d = str_replace(AppDadosEnum::FILME_TRAILER_YOUTUBE_5, $this->filme->trailer_5, $d);
            $d = str_replace(AppDadosEnum::FILME_IMDB_LINK, $this->filme->imdb->link, $d);
            $d = str_replace(AppDadosEnum::SERIE_ANOTACAO, $this->filme->serie_anotacao, $d);
            $d = str_replace(AppDadosEnum::FILME_OU_SERIE, $this->filme->filme_ou_serie, $d);
            $d = str_replace(AppDadosEnum::FILME_OU_SERIE_TEXTO, $this->filme->filme_ou_serie_texto, $d);
            return $d;
        }

        public function gerar_code_filmesviatorrent_info()
        {
            $code = \Config::get("sync.code_filmesviatorrentinfo.site");
            $this->codeFilmesViaTorrent_info = $this->trocar_dados($code);
            $this->gerar_download_m1();
            $this->filme->content = $this->codeFilmesViaTorrent_info;
        }

        public function gerar_code_filmesviatorrent_org()
        {
            $code = \Config::get("sync.code_filmesviatorrentorg.site");
            $this->codeFilmesViaTorrent_org = $this->trocar_dados($code);
            $this->gerar_download_m2();
            $this->filme->content = $this->codeFilmesViaTorrent_org;
        }

        public function gerar_code_piratefilmes()
        {
            $code = \Config::get("sync.code_piratefilmes.site");
            $this->codePirate = $this->trocar_dados($code);
            $this->gerar_download_m3();
            $this->filme->content = $this->codePirate;
        }

        public function gerar_code_filmesviatorrent_vip()
        {
            $code = \Config::get("sync.code_filmestorrentvip.site");
            $this->code_filmesviatorrent_vip = $this->trocar_dados($code);
            $this->gerar_download_m6();
            $this->filme->content = $this->code_filmesviatorrent_vip;
        }

        public function gerar_code_torrent_vip()
        {
            $content = [];
            if (!$this->filme->is_serie) {
                $content["custom_fields"] = [
                    [
                        "key" => "dt_backdrop",
                        "value" => $this->filme->theMovieDB->imagem_fundo_sem_link
                    ],
                    [
                        "key" => "dt_poster",
                        "value" => $this->filme->theMovieDB->imagem_capa_sem_link
                    ],
                    ["key" => "release_date", "value" => $this->filme->theMovieDB->data_lancamento],
                    ["key" => "idtmdb", "value" => $this->filme->theMovieDB->pegar_id()],
                    ["key" => "ids", "value" => $this->filme->imdb->id],
                    ["key" => "runtime", "value" => $this->filme->duracao],
                    [
                        "key" => "youtube_id",
                        "value" => $this->filme->trailer_3
                    ],
                    ["key" => "custom_informacoes", "value" => "true"],
                    ["key" => "idioma", "value" => $this->filme->audio],
                    ["key" => "formato", "value" => $this->filme->qualidade],
                    ["key" => "legenda", "value" => $this->filme->legenda],
                    ["key" => "nota_do_audio", "value" => $this->filme->qualidade_audio],
                    ["key" => "nota_do_video", "value" => $this->filme->qualidade_video],
                    ["key" => "original_title", "value" => $this->filme->theMovieDB->titulo_original],
                    ["key" => "imagenes", "value" => $this->filme->theMovieDB->imagem_fundo_sem_link],
                    ["key" => "tamanho", "value" => $this->filme->tamanho],
                    ["key" => "imdbRating", "value" => $this->filme->imdb->nota],
                    ["key" => "imdbVotes", "value" => $this->filme->imdb->votos],
                    ["key" => "Rated", "value" => $this->filme->imdb->rated],
                    ["key" => "vote_count", "value" => "" . $this->filme->theMovieDB->vote_count],
                    ["key" => "vote_average", "value" => "" . $this->filme->theMovieDB->vote_average],
                    ["key" => "dt_cast", "value" => $this->filme->theMovieDB->getFormatedCast()],
                    [
                        "key" => "Country",
                        "value" => $this->filme->imdb->country
                    ],
                ];

            }
            $this->gerar_download_m5($content);
            $content["terms_names"] = ["genres" => $this->filme->categorias_separadas,
                "dtcast" => $this->filme->theMovieDB->getCastNamesArray(),
                "dtdirector" => $this->filme->theMovieDB->getCastDirectorArray(),
                "dtyear" => FuncoesUteisFilme::getOnlyYear($this->filme->theMovieDB->data_lancamento)];
            $content["post_type"] = 'movies';
//        $content["comment_status"] = "open";
            $this->filme->custom_content = $content;
            $this->filme->content = $this->filme->sinopse;
        }

        private function gerar_download_m1()
        {
            if ($this->filme->is_serie) {
                if (!empty($this->filme->links_download_serie)) {
                    $html = "<div style='text-align:center;'>";
                    $html .= "<a href='https://legendario.org/busca?q=" . urlencode($this->filme->titulo) . "' target='_blank' rel='nofollow noopener noreferrer'>LEGENDA</a><br>";
                    foreach ($this->filme->links_download_serie as $texto => $dados) {
                        $html .= "<div><b>$texto</b></div>";
                        foreach ($dados as $texto_episodio => $links) {
                            $html .= "<div>$texto_episodio ";
                            $total = count($links);
                            $posicao = 1;
                            foreach ($links as $texto_link => $link_dado) {
                                $html .= "<a href='$link_dado' target='_blank' rel='nofollow noopener noreferrer'>$texto_link</a>";
                                if ($posicao != $total) {
                                    $html .= " | ";
                                }
                                $posicao++;
                            }
                            $html .= "</div>";
                        }
                    }
                    $html .= "</div></div>";
                    $this->codeFilmesViaTorrent_info .= $html;
                }
            } else {
                if (!empty($this->filme->links_downloads)) {
                    foreach ($this->filme->links_downloads as $links) {
                        foreach ($links->links as $link) {
                            $this->codeFilmesViaTorrent_info .= "\n<b>Baixar " . $this->filme->qualidade . " " . $link->qualidade_link . " " . FuncoesUteis::ajusta_download_texto($links->texto_links, $this->filme->qualidade, $link->qualidade_link) . "</b>\n";
                            $this->codeFilmesViaTorrent_info .= "<a href='" . $link->link . "' target='_blank' rel='noopener noreferrer'><img class='transparent alignnone' src='https://1.bp.blogspot.com/-I9z7Nj4RwyQ/WDsLsCJMLXI/AAAAAAAAGss/0XRZtMOjbs4IMuiyARkMTon3mrglJYwgwCLcB/s320/DownloadTorrent.png' width='203' height='44' /></a>";
                        }
                    }
                    $this->codeFilmesViaTorrent_info .= "</div>";
                }
            }

        }

        private function gerar_download_m2()
        {
            if ($this->filme->is_serie) {
                if (!empty($this->filme->links_download_serie)) {
                    $html = "<div style='text-align:center;'>";
                    $html .= "<a href='https://legendario.org/busca?q=" . urlencode($this->filme->titulo) . "' target='_blank' rel='nofollow noopener noreferrer'>LEGENDA</a><br>";
                    foreach ($this->filme->links_download_serie as $texto => $dados) {
                        $html .= "<div><b>$texto</b></div>";
                        foreach ($dados as $texto_episodio => $links) {
                            $html .= "<div>$texto_episodio ";
                            $total = count($links);
                            $posicao = 1;
                            foreach ($links as $texto_link => $link_dado) {
                                $html .= "<a href='$link_dado' target='_blank' rel='nofollow noopener noreferrer'>$texto_link</a>";
                                if ($posicao != $total) {
                                    $html .= " | ";
                                }
                                $posicao++;
                            }
                            $html .= "</div>";
                        }
                    }
                    $html .= "</div></hr>";
                    $this->codeFilmesViaTorrent_org .= $html;
                }
            } else {
                $this->codeFilmesViaTorrent_org .= "<div style='text-align: center;'>";
                if (!empty($this->filme->links_downloads)) {
                    foreach ($this->filme->links_downloads as $links) {
                        foreach ($links->links as $link) {
                            $this->codeFilmesViaTorrent_org .= "\n\n<strong>Baixar " . $this->filme->qualidade . " " . $link->qualidade_link . " " . FuncoesUteis::ajusta_download_texto($links->texto_links, $this->filme->qualidade, $link->qualidade_link) . "</strong>\n";
                            $this->codeFilmesViaTorrent_org .= "<a href='" . $link->link . "' target='_blank' rel='noopener noreferrer'><img class='transparent alignnone' src='http://www.filmesviatorrents.org/wp-content/uploads/2017/10/download-magnet.png' width='203' height='44' /></a>";
                        }
                    }
                    $this->codeFilmesViaTorrent_org .= "</div></hr>";
                }
            }
        }

        private function gerar_download_m3()
        {
            if ($this->filme->is_serie) {
                if (!empty($this->filme->links_download_serie)) {
                    $html = "<div style='text-align:center;'>";
                    $html .= "<a href='https://legendario.org/busca?q=" . urlencode($this->filme->titulo) . "' target='_blank' rel='nofollow noopener noreferrer'>LEGENDA</a><br>";
                    foreach ($this->filme->links_download_serie as $texto => $dados) {
                        $html .= "<div><b>$texto</b></div>";
                        foreach ($dados as $texto_episodio => $links) {
                            $html .= "<div>$texto_episodio ";
                            $total = count($links);
                            $posicao = 1;
                            foreach ($links as $texto_link => $link_dado) {
                                $html .= "<a href='$link_dado' target='_blank' rel='nofollow noopener noreferrer'>$texto_link</a>";
                                if ($posicao != $total) {
                                    $html .= " | ";
                                }
                                $posicao++;
                            }
                            $html .= "</div>";
                        }
                    }
                    $html .= "</div>";
                    $this->codePirate .= $html;
                }
            } else {
                $this->codePirate .= "<div style='text-align: center;'>";
                if (!empty($this->filme->links_downloads)) {
                    foreach ($this->filme->links_downloads as $links) {
                        foreach ($links->links as $link) {
                            $this->codePirate .= "\n\n<strong>Baixar " . $this->filme->qualidade . " " . $link->qualidade_link . " " . FuncoesUteis::ajusta_download_texto($links->texto_links, $this->filme->qualidade, $link->qualidade_link) . "</strong>\n";
                            $this->codePirate .= "<a href='" . $link->link . "' target='_blank' rel='noopener noreferrer'><img id='info-image' class='alignnone' title='Magnet Link' src='https://3.bp.blogspot.com/-IZqJiOGJHd0/Wj079qCLrPI/AAAAAAAABhY/JI6k3V85-VY1GlRUvh_sdl_wc9O07swUQCLcBGAs/s1600/magnet.png' alt='Magnet Link' width='158' height='33' data-wp-imgselect='1' /></a>";
                        }
                    }
                    $this->codePirate .= "</div></hr>";
                }
            }

        }

        public function gerarCodeKFilmes()
        {
            if ($this->filme->is_serie) {
                $content["custom_fields"] = [
                    [
                        "key" => "backdrop_hotlink",
                        "value" => $this->filme->theMovieDB->imagem_fundo_sem_link
                    ],
                    [
                        "key" => "poster_hotlink",
                        "value" => $this->filme->theMovieDB->imagem_capa_sem_link
                    ],
                    ["key" => "field_date", "value" => $this->filme->theMovieDB->data_lancamento],
                    ["key" => "field_date_last", "value" => $this->filme->theMovieDB->data_final],
                    ["key" => "field_id", "value" => $this->filme->theMovieDB->pegar_id()],
                    ["key" => "field_inproduction", "value" => $this->filme->theMovieDB->em_producao],
                    ["key" => "status", "value" => $this->filme->theMovieDB->status_serie],
                    ["key" => "tr_post_type", "value" => "2"],
                    ["key" => "field_runtime", "value" => $this->filme->theMovieDB->episodio_duracao],
                    ["key" => "field_title", "value" => $this->filme->theMovieDB->titulo_original],
                    [
                        "key" => "field_trailer",
                        "value" => $this->filme->trailer_7
                    ],
                    ["key" => "custom_informacoes", "value" => "true"],
                    ["key" => "custom_informacoes_audio", "value" => $this->filme->audio],
                    ["key" => "custom_informacoes_formato", "value" => $this->filme->formato],
                    ["key" => "custom_informacoes_legenda", "value" => $this->filme->legenda],
                    ["key" => "custom_informacoes_qualidade_audio", "value" => $this->filme->qualidade_audio],
                    ["key" => "custom_informacoes_qualidade_video", "value" => $this->filme->qualidade_video],
                    ["key" => "custom_informacoes_qualidade_item", "value" => $this->filme->qualidade],
                    ["key" => "custom_informacoes_tamanho", "value" => $this->filme->tamanho],
                    [
                        "key" => "custom_informacoes_tipo_audio",
                        "value" => FuncoesUteisFilme::retornar_tipo_audio($this->filme->audioFilme)
                    ],
                ];
            } else {
                $content["custom_fields"] = [
                    [
                        "key" => "backdrop_hotlink",
                        "value" => $this->filme->theMovieDB->imagem_fundo_sem_link
                    ],
                    [
                        "key" => "poster_hotlink",
                        "value" => $this->filme->theMovieDB->imagem_capa_sem_link
                    ],
                    ["key" => "field_date", "value" => $this->filme->theMovieDB->data_lancamento],
                    ["key" => "field_id", "value" => $this->filme->theMovieDB->pegar_id()],
                    ["key" => "field_imdbid", "value" => $this->filme->imdb->id],
                    ["key" => "field_release_year", "value" => $this->filme->theMovieDB->data_lancamento],
                    ["key" => "field_runtime", "value" => $this->filme->duracao],
                    ["key" => "field_title", "value" => $this->filme->theMovieDB->titulo_original],
                    ["key" => "custom_informacoes_qualidade_audio", "value" => $this->filme->qualidade_audio],
                    ["key" => "custom_informacoes_qualidade_video", "value" => $this->filme->qualidade_video],
                    [
                        "key" => "field_trailer",
                        "value" => $this->filme->trailer_7
                    ],
                ];
            }
            $this->gerar_download_kfilmes($content);
            $content["terms_names"] = ["category" => $this->filme->categorias_separadas, "cast" => $this->filme->theMovieDB->getCastNamesArray(),
                "directors" => $this->filme->theMovieDB->getCastDirectorArray()];
            $content["post_type"] = $this->filme->is_serie ? "series" : "movies";
            $content["comment_status"] = "open";
            $this->filme->custom_content = $content;
            $this->filme->content = $this->filme->sinopse;
        }

        public function gerarCode_custom_content_biz()
        {
            if ($this->filme->is_serie) {
                $content["custom_fields"] = [
                    [
                        "key" => "backdrop_hotlink",
                        "value" => $this->filme->theMovieDB->imagem_fundo_sem_link
                    ],
                    [
                        "key" => "poster_hotlink",
                        "value" => $this->filme->theMovieDB->imagem_capa_sem_link
                    ],
                    ["key" => "field_date", "value" => $this->filme->theMovieDB->data_lancamento],
                    ["key" => "field_date_last", "value" => $this->filme->theMovieDB->data_final],
                    ["key" => "field_id", "value" => $this->filme->theMovieDB->pegar_id()],
                    ["key" => "field_inproduction", "value" => $this->filme->theMovieDB->em_producao],
                    ["key" => "status", "value" => $this->filme->theMovieDB->status_serie],
                    ["key" => "tr_post_type", "value" => "2"],
                    ["key" => "field_runtime", "value" => $this->filme->theMovieDB->episodio_duracao],
                    ["key" => "field_title", "value" => $this->filme->theMovieDB->titulo_original],
                    [
                        "key" => "field_trailer",
                        "value" => "<iframe width=\"560\" height=\"315\" src=\"" . $this->filme->trailer_2 . "\" frameborder=\"0\" gesture=\"media\" allow=\"encrypted-media\" allowfullscreen></iframe>"
                    ],
                    ["key" => "custom_informacoes", "value" => "true"],
                    ["key" => "custom_informacoes_audio", "value" => $this->filme->audio],
                    ["key" => "custom_informacoes_formato", "value" => $this->filme->formato],
                    ["key" => "custom_informacoes_legenda", "value" => $this->filme->legenda],
                    ["key" => "custom_informacoes_qualidade_audio", "value" => $this->filme->qualidade_audio],
                    ["key" => "custom_informacoes_qualidade_video", "value" => $this->filme->qualidade_video],
                    ["key" => "custom_informacoes_qualidade_item", "value" => $this->filme->qualidade],
                    ["key" => "custom_informacoes_tamanho", "value" => $this->filme->tamanho],
                    [
                        "key" => "custom_informacoes_tipo_audio",
                        "value" => FuncoesUteisFilme::retornar_tipo_audio($this->filme->audioFilme)
                    ],
                ];
            } else {
                $content["custom_fields"] = [
                    [
                        "key" => "backdrop_hotlink",
                        "value" => $this->filme->theMovieDB->imagem_fundo_sem_link
                    ],
                    [
                        "key" => "poster_hotlink",
                        "value" => $this->filme->theMovieDB->imagem_capa_sem_link
                    ],
                    ["key" => "field_date", "value" => $this->filme->theMovieDB->data_lancamento],
                    ["key" => "field_id", "value" => $this->filme->theMovieDB->pegar_id()],
                    ["key" => "field_imdbid", "value" => $this->filme->imdb->id],
                    ["key" => "field_release_year", "value" => $this->filme->theMovieDB->data_lancamento],
                    ["key" => "field_runtime", "value" => $this->filme->duracao],
                    ["key" => "field_title", "value" => $this->filme->theMovieDB->titulo_original],
                    [
                        "key" => "field_trailer",
                        "value" => "<iframe width='560' height='315' src='" . $this->filme->trailer_4 . "' frameborder='0' gesture='media' allow='encrypted-media' allowfullscreen></iframe>"
                    ],
                    ["key" => "custom_informacoes", "value" => "true"],
                    ["key" => "custom_informacoes_audio", "value" => $this->filme->audio],
                    ["key" => "custom_informacoes_formato", "value" => $this->filme->formato],
                    ["key" => "custom_informacoes_legenda", "value" => $this->filme->legenda],
                    ["key" => "custom_informacoes_qualidade_audio", "value" => $this->filme->qualidade_audio],
                    ["key" => "custom_informacoes_qualidade_video", "value" => $this->filme->qualidade_video],
                    ["key" => "custom_informacoes_qualidade_item", "value" => $this->filme->qualidade],
                    ["key" => "custom_informacoes_tamanho", "value" => $this->filme->tamanho],
                    [
                        "key" => "custom_informacoes_tipo_audio",
                        "value" => FuncoesUteisFilme::retornar_tipo_audio($this->filme->audioFilme)
                    ],
                ];
            }

            $this->gerar_download_m4($content);
            $content["terms_names"] = ["category" => $this->filme->categorias_separadas];
            $content["post_type"] = $this->filme->is_serie ? "series" : "movies";
            $content["comment_status"] = "open";
            $this->filme->custom_content = $content;
            $this->filme->content = $this->filme->sinopse;
        }

        private function gerar_download_m4(&$content)
        {
            $count_links = 0;
            $html = "";
            if ($this->filme->is_serie) {
                if (!empty($this->filme->links_download_serie)) {
                    $html .= "<div style='text-align:center;'>";
                    foreach ($this->filme->links_download_serie as $texto => $dados) {
                        $html .= "<div><b>$texto</b></div>";
                        foreach ($dados as $texto_episodio => $links) {
                            $html .= "<div>$texto_episodio ";
                            $total = count($links);
                            $posicao = 1;
                            foreach ($links as $texto_link => $link_dado) {
                                $html .= "<a href='$link_dado' target='_blank' rel='nofollow noopener noreferrer'>$texto_link</a>";
                                if ($posicao != $total) {
                                    $html .= " | ";
                                }
                                $posicao++;
                            }
                            $html .= "</div>";
                        }
                    }
                    $html .= "</div>";
                }
            } else {
                $links_anterior = [];
                if (!empty($this->filme->links_downloads)) {
                    foreach ($this->filme->links_downloads as $links) {
                        foreach ($links->links as $link) {
                            $qualidade = "";
                            if ($link->qualidade_link == QualidadeLinkFilme::LINK_1080P && in_array($link->qualidade_link . $link->audio_link, $links_anterior)) {
                                $qualidade = "FULL";
                            } else {
                                $qualidade = $link->qualidade_link;
                            }
                            $html .= "<a class='link-download' target='_blank' rel='nofollow noopener noreferrer' href='" . $link->link . "'><i class='fa fa-magnet' aria-hidden='true'></i><b> Baixar</b>
                                <spanq>" . $qualidade . "</spanq><span>" . $link->audio_link . "</span>
                            </a>
                            <br>";
                            $count_links++;
                            $links_anterior[] = $link->qualidade_link . $link->audio_link;
                        }
                    }
                }
            }
            if ($this->filme->is_legendado()) {
                $content["custom_fields"][] = [
                    "key" => "legendas", "value" => "https://legendario.org/busca?q=" . urlencode(empty($this->filme->titulo_original) ? $this->filme->titulo_traduzido:$this->filme->titulo_original)
                ];
            }
            if ($this->filme->is_serie) {
                $content["custom_fields"][] = ["key" => "episodios_links", "value" => $html];
                $content["custom_fields"][] = [
                    "key" => "legendas", "value" => "https://legendario.org/busca?q=" . urlencode(empty($this->filme->titulo_original) ? $this->filme->titulo_traduzido:$this->filme->titulo_original)
                ];
            } else {
                $content["custom_fields"][] = ["key" => "custom_download", "value" => $html];
            }
        }
        private function gerar_download_kfilmes(&$content)
        {
            $count_links = 0;
            $html = "<div class='Wdgt'>
            <div class='Title'>".$this->filme->titulo_kfilme."</div>
            <div class='TPTblCn'>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>LINKS</th>
                            <th>SERVIDOR</th>
                            <th>IDIOMA</th>
                            <th>QUALIDADE | FORMATO</th>
                        </tr>
                    </thead><tbody>";
            if ($this->filme->is_serie) {
//                if (!empty($this->filme->links_download_serie)) {
//                    $html .= "<div style='text-align:center;'>";
//                    foreach ($this->filme->links_download_serie as $texto => $dados) {
//                        $html .= "<div><b>$texto</b></div>";
//                        foreach ($dados as $texto_episodio => $links) {
//                            $html .= "<div>$texto_episodio ";
//                            $total = count($links);
//                            $posicao = 1;
//                            foreach ($links as $texto_link => $link_dado) {
//                                $html .= "<a href='$link_dado' target='_blank' rel='nofollow noopener noreferrer'>$texto_link</a>";
//                                if ($posicao != $total) {
//                                    $html .= " | ";
//                                }
//                                $posicao++;
//                            }
//                            $html .= "</div>";
//                        }
//                    }
//                    $html .= "</div>";
//                }
            } else {
                $links_anterior = [];
                if (!empty($this->filme->links_downloads)) {
                    foreach ($this->filme->links_downloads as $links) {
                        foreach ($links->links as $link) {
                            if ($link->qualidade_link == QualidadeLinkFilme::LINK_1080P && in_array($link->qualidade_link . $link->audio_link, $links_anterior)) {
                                $qualidade = "FULL";
                            } else {
                                $qualidade = $link->qualidade_link;
                            }
                            $html .= " <tr>
                <td><span class='Num'>".($count_links + 1)."</span></td>
                <td><a rel='nofollow' target='_blank' href='" . $link->link . "' class='Button STPb'>Baixar</a></td>
                <td><span><b style='color: #14ad00;text-transform: uppercase;'>Torrent</b></span></td>
                <td><span>" . $link->audio_link . "</span></td>
                <td><span>" . $qualidade . " - ".$this->filme->formato."</span></td>
            </tr>";
                            $count_links++;
                            $links_anterior[] = $link->qualidade_link . $link->audio_link;
                        }
                    }
                }
            }
            $html .= "</tbody>
                </table>
            </div>
        </div>";
            if ($this->filme->is_legendado()) {
                $content["custom_fields"][] = [
                    "key" => "legendas", "value" => "https://legendario.org/busca?q=" . urlencode($this->filme->titulo_original)
                ];
            }
            if ($this->filme->is_serie) {
                $content["custom_fields"][] = ["key" => "episodios_links", "value" => $html];
                $content["custom_fields"][] = [
                    "key" => "legendas", "value" => "https://legendario.org/busca?q=" . urlencode($this->filme->titulo_original)
                ];
            } else {
                $content["custom_fields"][] = ["key" => "custom_download", "value" => $html];
            }
        }

        private function gerar_download_m5(&$content)
        {
            $count_links = 0;
            $html = "<div class='downloadsingle'><h2>Baixar " . $this->filme->titulo . "</h2>";
            $tamanhos = explode("|", $this->filme->tamanho);
            if (!empty($this->filme->links_downloads)) {
                foreach ($this->filme->links_downloads as $links) {
                    foreach ($links->links as $link) {
                        $tamanho_link = $tamanhos[$count_links] ?? "";
                        $html .= "<div class='corpodownload'>
                                <div class='tp_dw mobi01'>" . FuncoesUteis::ajusta_download_texto($links->texto_links, $this->filme->qualidade, $link->qualidade_link) . "</div>
                                <div class='tp_dw mobi02'>" . $link->qualidade_link . "</div>
                                <div class='tp_dw mobi03'>" . $this->filme->qualidade . "</div>
                                <div class='tp_dw mobi04'>" . $tamanho_link . "</div>
                                <div class='tp_dw mobi05'><a href='" . $link->link . "'>BAIXAR</a></div></div>";
                        $count_links++;
                    }
                }
            }
            $html .= "</div>";
            $content["custom_fields"][] = ["key" => "custom_download", "value" => $html];
        }

        private function gerar_download_m6()
        {
            if ($this->filme->is_serie) {
                if (!empty($this->filme->links_download_serie)) {
                    $html = "<div style='text-align:center;'>";
                    $html .= "<a href='https://legendario.org/busca?q=" . urlencode($this->filme->titulo) . "' target='_blank' rel='nofollow noopener noreferrer'>LEGENDA</a><br>";
                    foreach ($this->filme->links_download_serie as $texto => $dados) {
                        $html .= "<div><b>$texto</b></div>";
                        foreach ($dados as $texto_episodio => $links) {
                            $html .= "<div>$texto_episodio ";
                            $total = count($links);
                            $posicao = 1;
                            foreach ($links as $texto_link => $link_dado) {
                                $html .= "<a href='$link_dado' target='_blank' rel='nofollow noopener noreferrer'>$texto_link</a>";
                                if ($posicao != $total) {
                                    $html .= " | ";
                                }
                                $posicao++;
                            }
                            $html .= "</div>";
                        }
                    }
                    $html .= "</div></div>";
                    $this->code_filmesviatorrent_vip .= $html;
                }
            } else {
                if (!empty($this->filme->links_downloads)) {
                    foreach ($this->filme->links_downloads as $links) {
                        foreach ($links->links as $link) {
                            $this->code_filmesviatorrent_vip .= "\n<b>Baixar " . $this->filme->qualidade . " " . $link->qualidade_link . " " . FuncoesUteis::ajusta_download_texto($links->texto_links, $this->filme->qualidade, $link->qualidade_link) . "</b>\n";
                            $this->code_filmesviatorrent_vip .= "<a href='" . $link->link . "' target='_blank' rel='noopener noreferrer'><img class='transparent alignnone' src='https://filmestorrent.vip/wp-content/uploads/2019/10/download_magnet2.png' width='203' height='44' /></a>";
                        }
                    }
                    $this->code_filmesviatorrent_vip .= "</div>";
                }
            }
        }
    }
