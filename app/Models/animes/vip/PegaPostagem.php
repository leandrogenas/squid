<?php


    namespace App\Models\animes\vip;


    use App\Enums\Sites;
    use App\Models\wordpress\WPress;
    use Illuminate\Support\Facades\Log;
    use Thujohn\Twitter\Facades\Twitter;

    class PegaPostagem
    {
        public $log;
        private $wp;

        /**
         * PegaPostagem constructor.
         */
        public function __construct()
        {
            $this->log = "";
            $this->wp = new WPress();
        }

        public function start()
        {
            try {
                $animes = PostVip::published()->type("post")->get(["ID"]);
                foreach ($animes as $anime) {
                    $ids = $this->getEpisodiosID($anime->ID);
                    $this->atualizar_postagem($anime->ID, $ids);
                }
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->log .= $ex->getMessage() . "\n\n\n";
            }
        }

        public function updatePostagemUnica($id_anime)
        {
            $ids = $this->getEpisodiosID($id_anime);
            $this->atualizar_postagem($id_anime, $ids);
        }

        private function atualizar_postagem($id_anime, array $ids_episodios)
        {
            $content = $this->wp->getPostPorSite(Sites::ANIMESONLINE_VIP, $id_anime);
            $custom_episodio_id = $this->wp->searchCustomField($content["custom_fields"], "episodios_relacao");
            $lista_episodio = implode(",", $ids_episodios);
            if ($custom_episodio_id != false) {
                $content["custom_fields"] = [
                    ["id" => $custom_episodio_id['id'], "key" => "episodios_relacao", "value" => $lista_episodio]
                ];
            } else {
                $content["custom_fields"] = [
                    ["key" => "episodios_relacao", "value" => $lista_episodio]
                ];
            }
            $this->wp->editPostPorSite(Sites::ANIMESONLINE_VIP, $id_anime, $content, false);
        }


        private function getEpisodiosID($id)
        {
            $lista = [];
            try {
                $episodios = PostVip::published()->type("episodio")->hasMeta("episodio_anime", $id)->limit(10)->get(['ID'])->sortBy(function ($episodio, $key) {
                    return $episodio->meta->episodio_ep;
                });
                foreach ($episodios as $episodio) {
                    $lista[] = $episodio->ID;
                }
            } catch (\Throwable $ex) {
                Log::info("Houve um erro ao pegar um ou mais ID no anime ID" . $id);
                Log::error($ex);
                $this->log .= "Houve um erro ao pegar um ou mais ID no anime ID $id, ERRO: " . $ex->getMessage() . "\n\n\n";
            }
            return $lista;
        }
    }
