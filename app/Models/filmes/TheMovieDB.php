<?php


    namespace App\Models\filmes;


    use App\Utils\FuncoesUteis;
    use voku\helper\HtmlDomParser;
    use voku\helper\SimpleHtmlDomInterface;

    class TheMovieDB
    {
        private $link;
        public $sinopse, $nota, $imagem_capa_link, $titulo, $imagem_fundo, $imagem_fundo_sem_link, $imagem_capa_sem_link, $titulo_original, $data_lancamento, $id, $is_serie, $data_inicial, $data_final, $em_producao, $status_serie, $episodio_duracao, $vote_count, $vote_average, $casts, $crews, $numero_de_episodios, $generos;

        /**
         * TheMovieDB constructor.
         * @param $id
         * @param $is_serie
         */
        public function __construct($id, $is_serie)
        {
            $this->id = $id;
            $this->is_serie = $is_serie;
        }

        /**
         * @param $name
         * @param array $lista
         * @return bool
         */
        private static function search_all_custom($name, array &$lista)
        {
            $nome = FuncoesUteis::remover_ascentos($name);
            $json_dados = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/search/multi?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR&query=" . urlencode($nome))->text();
            $dados = json_decode($json_dados);
            foreach ($dados->results as $resultado) {
                $lista["lista_themovie"][] = ["name" => $resultado->name ?? $resultado->title, "id" => $resultado->id, "media_type" => $resultado->media_type ?? "tv"];
            }
            return count($dados->results) > 0;
        }


        public function pegar_id()
        {
//        return FuncoesUteis::useRegex('/[0-9].*[0-9]/m', $this->link)[0];
            return $this->id;
        }

        public static function procurar_filme($nome_filme)
        {
            try {
                $json_dados = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/search/movie?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR&query=" . urlencode($nome_filme))->text();
                $dados = json_decode($json_dados);
                $arr = [];
                $arr["nome"] = $dados->results[0]->title;
                $arr['nome_sem_erro'] = $dados->results[0]->title;
                $arr["id"] = $dados->results[0]->id;
                return $arr;
            } catch (\Exception $ex) {
                \Log::error($ex);
                $arr["nome"] = "Nenhum Link encontrado para o nome: $nome_filme";
                $arr['nome_sem_erro'] = $nome_filme;
                $arr["id"] = "";
                return $arr;
            }
        }

        public static function procurar_serie($nome_serie)
        {
            try {
                $json_dados = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/search/tv?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR&query=" . urlencode($nome_serie))->text();
                $dados = json_decode($json_dados);
                $arr = [];
                $arr["nome"] = $dados->results[0]->original_name;
                $arr["id"] = $dados->results[0]->id;
                return $arr;
            } catch (\Exception $ex) {
                \Log::error($ex);
                $arr["nome"] = "Nenhum Link encontrado para o nome: $nome_serie";
                $arr["id"] = "";
                return $arr;
            }
        }

        public static function procurar_filme_ou_serie($nome_filme)
        {
            try {
                $nome = FuncoesUteis::remover_ascentos($nome_filme);
                $json_dados = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/search/multi?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR&query=" . urlencode($nome))->text();
                $dados = json_decode($json_dados);
                $arr = [];
                $arr["nome"] = $dados->results[0]->name ?? $dados->results[0]->title;
                $arr["id"] = $dados->results[0]->id;
                return $arr;
            } catch (\Exception $ex) {
                \Log::error($ex);
                $arr["nome"] = "Nenhum Link encontrado para o nome: $nome_filme";
                $arr["id"] = "";
                return $arr;
            }
        }

        public static function search_all($name,$search2 = "")
        {
            $lista = [];
            try {
                $resultado = self::search_all_custom($name, $lista);
                if(!$resultado){
                    if(!empty($search2)){
                        self::search_all_custom($search2, $lista);
                    }
                }
                return $lista;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return $lista;
            }
        }

        public static function getimagens($id,$type = "tv")
        {
            $lista = [];
            try {
                try {
                    $json_dado = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/$type/" . $id . "?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR")->text();
                } catch (\Throwable $ex) {
                    $json_dado = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/movie/" . $id . "?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR")->text();
                }
                $dado = json_decode($json_dado);
                $padrao_link = "https://image.tmdb.org/t/p/w300_and_h450_bestv2";
                $lista["lista"][] = ["imagem" => $padrao_link . $dado->poster_path, "name" => $dado->name ?? ""];
                if(property_exists($dado,"seasons")){
                    foreach ($dado->seasons as $season) {
                        $lista["lista"][] = ["imagem" => $padrao_link . $season->poster_path, "name" => $season->name ?? ""];
                    }
                }
                return $lista;
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return $lista;
            }
        }

        public function getFormatedCast()
        {
            $texto = "";
            $count = 0;
            foreach ($this->casts as $cast) {
                $texto .= "[";
                $texto .= $cast->profile_path;
                $texto .= ";" . $cast->name . "]";
                $count++;
                if ($count == 10) {
                    break;
                }
            }
            return $texto;
        }

        public function pegar_dados()
        {
            try {
                if ($this->is_serie) {
                    $json_dado = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/tv/" . $this->id . "?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR")->text();
                    $dado = json_decode($json_dado);
                    $this->titulo = $dado->original_name;
                    $this->titulo_original = $dado->original_name;
                    $this->imagem_capa_link = "https://image.tmdb.org/t/p/w1280" . $dado->poster_path;
                    $this->imagem_fundo = "https://image.tmdb.org/t/p/w1280" . $dado->backdrop_path;
                    $this->imagem_capa_sem_link = $dado->poster_path;
                    $this->imagem_fundo_sem_link = $dado->backdrop_path;
                    $this->sinopse = $dado->overview;
                    $this->data_lancamento = $dado->first_air_date;
                    $this->data_inicial = $dado->first_air_date;
                    $this->data_final = $dado->last_air_date;
                    $this->em_producao = $dado->in_production ? "1" : "0";
                    $this->status_serie = $dado->status;
                    $this->episodio_duracao = $dado->episode_run_time[0];
                    $this->numero_de_episodios = $dado->number_of_episodes;
                    $this->generos = $dado->genres;
                    $this->nota = $dado->vote_average;
                } else {
                    $json_dado = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/movie/" . $this->id . "?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR")->text();
                    $dado = json_decode($json_dado);
                    $this->titulo = $dado->title;
                    $this->titulo_original = $dado->original_title;
                    $this->imagem_capa_link = "https://image.tmdb.org/t/p/w1280" . $dado->poster_path;
                    $this->imagem_fundo = "https://image.tmdb.org/t/p/w1280" . $dado->backdrop_path;
                    $this->imagem_capa_sem_link = $dado->poster_path;
                    $this->imagem_fundo_sem_link = $dado->backdrop_path;
                    $this->sinopse = $dado->overview;
                    $this->data_lancamento = $dado->release_date;
                    $this->data_inicial = $dado->release_date;
                    $this->data_final = $dado->release_date;
                    $this->vote_count = $dado->vote_count;
                    $this->vote_average = $dado->vote_average;
                    $this->pegar_cast();
                }

            } catch (\Exception $ex) {
                \Log::error($ex);
            }
        }

        private function pegar_cast()
        {
            $json_dado = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/movie/" . $this->id . "/credits?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR")->text();
            $dado = json_decode($json_dado);
            $this->casts = $dado->cast;
            $this->crews = $dado->crew;
        }

        /**
         * @return array
         */
        public function getCastNamesArray()
        {
            $casts = [];
            for ($i = 0; $i < count($this->casts); $i++) {
                $casts[] = $this->casts[$i]->name;
                if ($i == 10) {
                    break;
                }
            }
            return $casts;
        }

        public function getCastDirectorArray()
        {
            $diretor = [];
            foreach ($this->crews as $crew) {
                if ($crew->department == "Directing") {
                    $diretor[] = $crew->name;
                }
            }
            return $diretor;
        }

        public function getGenerosSeparados()
        {
            $generos = [];
            if(!empty($this->generos)){
                foreach ($this->generos as $genero) {
                    $generos[] = $genero->name;
                }
            }
            return $generos;
        }

        public function getEpisodioDetalhes($temporada, $episodio,$language = "pt-BR")
        {
            $json_dado = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/tv/" . $this->id . "/season/$temporada/episode/$episodio?api_key=7d081946b53653ba225dd7f2b886d6de&language=$language")->text();
            return json_decode($json_dado);
        }

        public function getTemporadaDetalhes($temporada_numero)
        {
            $json_dado = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/tv/" . $this->id . "/season/$temporada_numero?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR")->text();
            return json_decode($json_dado);
        }
    }
