<?php


    namespace App\Models\IA;


    use App\Enums\PostagemStatus;
    use App\Models\filmes\TheMovieDB;
    use App\Models\IMDB;
    use App\Utils\FuncoesUteis;

    class FazerPostagem
    {
        public function postar_filme(array $links)
        {
            foreach ($links as $link) {
                try {
                    $nome = FuncoesUteis::pegar_titulo_filme($link["link"]);
                    if (!empty($nome)) {
                        $dado_themovie = TheMovieDB::procurar_filme($nome);
                        $dado_imdb = IMDB::procurar_filme($nome);
                        if (isset($dado_themovie['nome']) && isset($dado_imdb["nome"])) {
                            //continua
                        } else {
                            $this->savePostagem(0,$link,PostagemStatus::ERRO,"Faltou algum dado do themovie ou IMDB");
                        }
                    } else {
                        $this->savePostagem(0,$link,PostagemStatus::ERRO,"Não foi possível pegar o nome do filme!");
                    }
                } catch (\Throwable $ex) {
                    \Log::error($ex);
                    $this->savePostagem(0,$link,PostagemStatus::ERRO,"Houve um erro! Erro: " . $ex->getMessage());
                }
            }
        }

        private function savePostagem($post_id,array $link, $status, $log = "")
        {
            Postagem::create([
                "post_id" => "0",
                "title" => $link["text"],
                "url" => $link["link"],
                "status" => PostagemStatus::ERRO,
                "log" => $log
            ]);
        }
    }
