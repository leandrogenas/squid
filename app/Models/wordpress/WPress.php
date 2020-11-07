<?php


    namespace App\Models\wordpress;

    use HieuLe\WordpressXmlrpcClient\WordpressClient;
    use Illuminate\Support\Carbon;

    /**
     * Nova classe para trabalhar com wordpress de maneira simples.
     * Class WPress
     * @package App\Models\wordpress
     */
    class WPress
    {
        private $list_cliente = [];

        public function addPostPorSite($site, $title, $descricao, $content, $data = null)
        {
            $wpCliente = $this->getClient($site);
            if(!is_null($data)){
                $content['post_date'] = $wpCliente->createXMLRPCDateTime($data);
            }
            return $this->addPost($wpCliente,$title,$descricao,$content);
        }

        public function getPostPorSite($site,$post_id){
            $wpCliente = $this->getClient($site);
            return $this->getPost($wpCliente,$post_id);
        }

        private function getPost(WordpressClient $client, $post_id){
            return $client->getPost($post_id);
        }

        public function editPostPorSite($site,$post_id,$content,$update_data = true){
            $wpCliente = $this->getClient($site);
            if($update_data){
                $content['post_date'] = $wpCliente->createXMLRPCDateTime(Carbon::now()->toDateTime());
            }
            return $this->editPost($wpCliente,$post_id,$content);
        }

        public function uploadImagemPorSite($site,$imagem){
            $wpCliente = $this->getClient($site);
            return $this->uploadImagem($wpCliente,$imagem);
        }

        private function uploadImagem(WordpressClient $client, $imagem){
            $path_imagem = "../public/img/baixadas/$imagem";
            $mime = 'image/png';
            $data = file_get_contents($path_imagem);
            return $client->uploadFile($imagem,$mime,$data,true);
        }

        private function addPost(WordpressClient $client,$title, $descricao, $content)
        {
            return $client->newPost($title,$descricao,$content);
        }

        private function editPost(WordpressClient $client,$post_id,$content){
            return $client->editPost($post_id,$content);
        }

        /**
         * @param $site
         * @return WordpressClient|mixed
         */
        private function getClient($site)
        {
            if (key_exists($site, $this->list_cliente)) {
                return $this->list_cliente[$site];
            } else {
                $cliente = $this->fazerLogin($site);
                $this->list_cliente[$site] = $cliente;
                return $cliente;
            }
        }

        private function fazerLogin($site)
        {
            $user = \Config::get("sync.login_$site.user");
            $password = \Config::get("sync.login_$site.password");
            $url = \Config::get("sync.login_$site.url");
            $cliente = new WordpressClient();
            $cliente->setCredentials($url, $user, $password);
            return $cliente;
        }

        /**
         * procura em um array com custom fields, e retorna o field pela key procurada!
         * @param array $values
         * @param $key_search
         * @return bool|mixed
         */
        public function searchCustomField(array $values, $key_search){
            foreach ($values as $value){
                if($value["key"] === $key_search){
                    return $value;
                }
            }
            return false;
        }
    }
