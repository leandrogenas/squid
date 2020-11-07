<?php


    namespace App\Models\series;


    use App\Models\series\Utils\GerarContentSerie;
    use App\Models\wordpress\WordPress;
    use Arcanedev\LogViewer\Entities\Log;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Storage;

    class PublicaSerie
    {

        private $episodios_postados = [];
        private $episodios_imagens = [];
        private $dados_para_salvar_episodio = [];

        private function atualizar_progresso($idthemovie)
        {
            \Session::put("progresso", $idthemovie);
            \Session::save();
        }

        private function save_json_season($dado_json)
        {
            \Session::put("json", $dado_json);
            \Session::save();
        }

        public function publicar(WordPress $wordPress, $series = [], $sites = [])
        {
            /**@var Serie $serie */
            foreach ($series as $serie) {
                $serie->carregar_dados();
                $serie->pegar_links_para_episodio();
                $gerar = new GerarContentSerie($serie);
                $count = 1;
                foreach ($serie->temporadas as $temporada) {
                    foreach ($sites as $site) {
                        if ($temporada->ja_existe_postagem) {
                            $this->publicar_episodios($wordPress, $temporada, $gerar, $site);
                        } else {
                            $this->publica_temporada($temporada, $gerar, $wordPress, $site,$count);
                            $this->publicar_episodios($wordPress, $temporada, $gerar, $site);
                        }
                        $this->atualizar_temporada_episodios($site, $temporada, $gerar, $wordPress);

                        $this->episodios_postados = [];
                        $this->episodios_imagens = [];
                    }
                    $this->atualizar_progresso($serie->theMovieDB->id);
                    $count++;
                }
                //$this->salvar_dados();
                $this->dados_para_salvar_episodio = [];
            }
            return true;
        }

        private function salvar_dados()
        {
            $dados_json = json_encode($this->dados_para_salvar_episodio);
            $dado = $dados_json . "\n\r===================";
            Storage::append("lista_episodios.txt", $dado);
            $this->save_json_season($dados_json);
        }

        private function atualizar_temporada_episodios($site, Temporadas $temporada, GerarContentSerie $gerar, WordPress $wordpress)
        {
            $anterior_content = $wordpress->getPostGenericoPorSite($site, $temporada->post_id);
            $content = $gerar->updateContentSerie($this->episodios_postados, $anterior_content);
            $temporada->content = $content;
            $wordpress->editPostGenerico($site, $temporada->post_id, $temporada->content);
        }


        private function publica_temporada(Temporadas &$temporada, GerarContentSerie $gerar, WordPress $wordPress, $site,$index = 1)
        {
            $temporada->baixar_imagem();
            $temporada->preparar_imagens_por_site($site);
            $resultado = $wordPress->uploadImagem($site, $temporada->imagem_pronta);
            $imagem_id = $resultado["id"];
            $temporada->content = $gerar->gerarContentTemporada($temporada, $imagem_id);
            $data = Carbon::now()->addSeconds($index)->toDateTime();
            $post_id = $wordPress->addPostGenericoPorSite($site, $temporada->getTituloPronto(), $temporada->descricao, $temporada->content,$data);
            $temporada->post_id = $post_id;
        }

        private function publicar_serie(Serie &$serie, GerarContentSerie $gerar, WordPress $wordPress, $site)
        {
            $serie->baixar_imagem();
            $serie->preparar_imagens_por_site($site);
            $resultado = $wordPress->uploadImagem($site, $serie->img_site);
            $imagem_id = $resultado["id"];
            $serie->content = $gerar->gerarContent($imagem_id);
            $post_id = $wordPress->addPostPorSiteSomenteSerie($site, $serie);
            $serie->post_pai = $post_id;
        }

        private function publica_episodio(Episodio &$episodio, Temporadas &$temporada, GerarContentSerie $gerar, WordPress $wordPress, $site,$index =1)
        {
            if (array_key_exists($temporada->temporada_numero . $episodio->episodio, $this->episodios_imagens)) {
                $imagem_id = $this->episodios_imagens[$temporada->temporada_numero . $episodio->episodio];
            } else {
                $resultado = $episodio->baixar_imagem();
                if($resultado){
                    $episodio->preparar_imagens_por_site($site);
                    $resultado = $wordPress->uploadImagem($site, $episodio->imagem_pronta);
                    $imagem_id = $resultado["id"];
                }else{
                    $imagem_id = "10272";
                }
                $this->episodios_imagens[$temporada->temporada_numero . $episodio->episodio] = $imagem_id;
            }
            $episodio->content = $gerar->gerarContentEpisodio($episodio, $temporada, $imagem_id, $this->dados_para_salvar_episodio);
            $data = Carbon::now()->addSeconds($index)->toDateTime();
            $post_id = $wordPress->addPostGenericoPorSite($site, $episodio->getTituloPronto(), $episodio->descricao, $episodio->content,$data);
            $this->episodios_postados[$episodio->tipo][] = (int)$post_id;
        }

        /**
         * @param WordPress $wordPress
         * @param Temporadas $temporada
         * @param GerarContentSerie $gerar
         * @param $site
         */
        private function publicar_episodios(WordPress $wordPress, Temporadas &$temporada, GerarContentSerie $gerar, $site): void
        {
            $count = 2;
            foreach ($temporada->episodios as $episodio) {
                $this->publica_episodio($episodio, $temporada, $gerar, $wordPress, $site,$count);
                $count++;
            }
        }
    }
