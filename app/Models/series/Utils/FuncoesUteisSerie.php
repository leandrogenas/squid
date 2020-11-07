<?php


    namespace App\Models\series\Utils;


    use App\Models\filmes\TheMovieDB;
    use App\Models\series\Serie;
    use App\Utils\FuncoesUteis;

    class FuncoesUteisSerie
    {
        protected function preparar_imagens(Serie $serie)
        {
            $img = "img/temp.png";
            FuncoesUteis::baixar_imagem($serie->url_imagem_themovie, $img);
        }

        protected function verificar_e_usar_themoviedb(TheMovieDB $theMovieDB, Serie $serie)
        {
            if (!empty($theMovieDB)) {
                $theMovieDB->pegar_dados();
                $serie->descricao = $theMovieDB->sinopse;
                $serie->url_imagem_themovie = $theMovieDB->imagem_capa_link;
                $serie->titulo_traduzido = $theMovieDB->titulo;
                $serie->titulo = empty($serie->serie_name) ? $theMovieDB->titulo : $serie->serie_name;
                $generos = $theMovieDB->getGenerosSeparados();
                if (!empty($generos)) {
                    $serie->categorias_separadas = $generos;
                }
            }
        }
    }
