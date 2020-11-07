<?php


    namespace App\Models\feed;


    use App\Enums\FeedLinkStatus;
    use App\Enums\TipoLinksFeed;
    use Illuminate\Support\Facades\Log;
    use phpDocumentor\Reflection\Types\Self_;
    use Stevenmaguire\Services\Trello\Client;
    use Thujohn\Twitter\Facades\Twitter;
    use voku\helper\HtmlDomParser;
    use willvincent\Feeds\Facades\FeedsFacade;

    class ProcessFeeds
    {
        private $lista_links = [];
        private $lista = [];

        /**
         * ProcessFeeds constructor.
         */
        public function __construct()
        {
            $this->init();
        }

        private function init()
        {
            $this->lista = [
                "ANIMESVIP" => [
                    'nimbus' => 'https://nimbusweb.me/s/share/4491212/0o0k1yg5c4nzsd2eqe43',
                    'feed' => [
                        TipoLinksFeed::PINTEREST => 'https://br.pinterest.com/animesonlinevip/feed.rss',
                        TipoLinksFeed::WORDPRESS => 'https://animesonlinevip.video.blog/feed',
                        TipoLinksFeed::TUMBLR => 'https://animesonlinevip.tumblr.com/rss',
                        TipoLinksFeed::BLOGGER => 'https://animesonline-vip.blogspot.com/rss.xml',
                        TipoLinksFeed::WEEBLY => 'https://animesonlinevip.weebly.com/blog/feed',
                        TipoLinksFeed::WORDPRESS => 'https://animesonline.vip/episodio/rss',
                    ],
                    'evernote' => '1KXZKqswHtipErw2IoxfOqjnvLMPmmtIa',
                    'drive' => ['1APhDfbupVwEESj7ZStaozP3gLgVjF01t', '1DLZxWm1ScENZ8oezb-S-_euCCKCQttio'],
                    'twitter' => ['token' => '1236170400268193792-NQUb8dh39rbo3daatkiVQ7742lJ7tm', 'secret' => 'hFfeBF459LaUMnSwCNJVZ1xjDRBDizzRY9PBONYAg6dzH'],
                    "twitter_link" => 'https://twitter.com/VipAnimes/status/',
                    'trello' => ["key" => "31837f5685fdd47710d9dd4cd9db7076", "token" => "910d2928cd74326dfcb9cdff5a287e6626b2949a7a79c5735104d3162fda6b92", "quadro" => "5d929b49569ec07d40a4f688"],
                    'BitLy'=>['key'=>'31022ea15416da6c3452a9513356f4cdf6cff74d','group'=>'Bk8k0lZEvuf'],
                    'narro'=>'db8871be-7cb1-4759-a3c9-77843fe93777711c68d1-7082-46db-871d-867a1db5e95e979aa54a-eec1-4eb9-ae22-5ef4246ed8d4'
                ],
                "FILMESVIATORRENTBIZ" => [
                    'nimbus' => 'https://nimbusweb.me/s/share/4487140/tfoyvb3mfehuh850tiqw',
                    'feed' => [
                        TipoLinksFeed::WORDPRESS => 'https://filmesemtorrentgratis.wordpress.com/feed',
                        TipoLinksFeed::BLOGGER => 'https://filmesviatorrent-biz.blogspot.com/rss.xml',
                        TipoLinksFeed::TUMBLR => 'https://filmesviatorrent.tumblr.com/rss',
                        TipoLinksFeed::PINTEREST => 'https://br.pinterest.com/filmesviatorrents/feed.rss',
                        TipoLinksFeed::WEEBLY => 'https://filmesviatorrents.weebly.com/blog/feed',
                        TipoLinksFeed::WORDPRESS => 'https://filmesviatorrents.biz/rss',
                    ],
                    'evernote' => '18Bp8qnOouHmjGwlD2-S5cJunFTgtkl1m',
                    'drive' => ['164tCmKPn88frsMIjbeOmHax02I7j87vn'],
                    'twitter' => ['token' => '1136422790884728832-a8knTZUgmBpgnn5Tw7ibhp5I96xK2H', 'secret' => 'Sqfo0nbh7iQ3lfk5gku8fP6po7RR7BCTLwEyV8y6JUKET'],
                    "twitter_link" => 'https://twitter.com/FilmesVia/status/',
                    'trello' => ["key" => "1cc33f1771b68564a0326d53f35dcd02", "token" => "a91c13f38c5604f3b4c79419e6d72856bbc93a437d4de9de5d7bab3ff98042bf", "quadro" => "5f31a7e56337de77f8228e18"],
                    'BitLy'=>['key'=>'08bd6ccad6b1e88a0b67b0d930670cc2cbda38f4','group'=>'Bk8lgUo8VmR'],
                    'narro'=>'829ec30e-36e6-48ff-9939-82e032e58d9e4c316d89-d983-428c-9bc6-c9ca9ad1cf064409d23e-b127-4da4-828a-1ee8aa6f2f69'
                ],
                "FILMESVIATORRENTVIP" => [
                    'nimbus' => 'https://nimbusweb.me/s/share/4487530/acb4l6x78gdp65tu782r',
                    'feed' => [TipoLinksFeed::PINTEREST => 'https://br.pinterest.com/baixarfilmestorrents/feed.rss', TipoLinksFeed::TUMBLR => 'https://downloadfilmestorrent.tumblr.com/rss', TipoLinksFeed::BLOGGER => 'https://filmestorrentsvip.blogspot.com/rss.xml', TipoLinksFeed::WORDPRESS => 'https://filmestorrentvip.wordpress.com/feed',
                        TipoLinksFeed::WEEBLY=>'https://filmestorrentvip.weebly.com/blog/feed',
                        TipoLinksFeed::WORDPRESS => 'https://filmestorrent.vip/feed',
                    ],
                    'evernote' => '1R5Ink7of4n9JY8qTV6nsB6WXztCUkY6r',
                    'drive' => ['1eq9vsztv7XVzE-6e4MfXz-UWBda4x6GU'],
                    'twitter' => ['token' => '1197323066965200897-qs0QIksCXrNgOFV5LzBLYNJZXkqYRv', 'secret' => 'v5tZaMJJi7uNBn2ZqS95yjFtmrY903FTe1TsjEf6rA8cn'],
                    "twitter_link" => 'https://twitter.com/filmetorrentvip/status/',
                    'trello' => ["key" => "d081814d45e16fec18893c2127fbeea0", "token" => "15f1b8e523727fa2b3e8ff72124786ecd7861789422132c2e03202293d7af081", "quadro" => "5f31ab8b72b4c525f1ad32b8"],
                    'BitLy'=>['key'=>'2a3b68389b5ee48080e4e1e540668657d456bb18','group'=>'Bk8lgKiYzqg'],
                    'narro'=>'bdc72cf5-e853-44a0-99c1-8653738636f2f217beae-ef09-4bec-af2d-611f6af3173521030ec1-1171-4002-b5f3-8a9a76a62796'
                ],
                "JOGOSTORRENT" => [
                    'nimbus' => 'https://nimbusweb.me/s/share/4487540/mb67w3w59gdgsx6iswg0',
                    'feed' => [TipoLinksFeed::PINTEREST => 'https://br.pinterest.com/jogostorrentsite/feed.rss', TipoLinksFeed::TUMBLR => 'https://jogostorrentsite.tumblr.com/rss', TipoLinksFeed::BLOGGER => 'https://jogostorrentsite.blogspot.com/rss.xml', TipoLinksFeed::WORDPRESS => 'https://jogostorrents.home.blog/feed',
                        TipoLinksFeed::WEEBLY=>'https://jogostorrentsites.weebly.com/blog/feed',
                        TipoLinksFeed::WORDPRESS => 'https://jogostorrents.site/feed',
                    ],
                    'evernote' => '1Wv2hs5iNBeeoix6CreY5bgnEgrcDbA7r',
                    'drive' => ['1tDQUA-_yy5-CX6Y0AA8CcoE4SRkd51Vw'],
                    'twitter' => ['token' => '1198719495394775047-DnWQlMTSNVgVgUarRbvwLkdU0zMuGa', 'secret' => 'IJUrqJtGZs0z8ITeydhF8tcA1xqmTtuYCLIYclCrUKxgU'],
                    "twitter_link" => 'https://twitter.com/torrents_jogos/status/',
                    'trello' => ["key" => "ae25cd47e894feea61c42e2ffec59863", "token" => "d85ce4f97fa3394a6b164df00df67b6542f987fc92f8bbff73df4991e901a9ad", "quadro" => "5f31a4891d76a939a8b25f04"],
                    'BitLy'=>['key'=>'c3bd5d6c4ea98ba5babeaf6b2d062b37234a17fb','group'=>'Bk8lg7FStYM'],
                    'narro'=>'67bb5e15-17d9-43a8-9d46-996791e3a63702c50c3b-8f0c-4e3c-a5aa-051fb1277c112dc2dec8-b8ce-4b3c-803c-40e0d872fb7d'
                ],
                "SERIESONLINE" => [
                    'nimbus' => 'https://nimbusweb.me/s/share/4454920/ylvq3nxld7x6fqcqq6u1',
                    'feed' => [TipoLinksFeed::WORDPRESS => 'https://seriesonlinepro.wordpress.com/feed', TipoLinksFeed::BLOGGER => 'https://seriesonlinepro.blogspot.com/rss.xml', TipoLinksFeed::TUMBLR => 'https://seriesonlinepro.tumblr.com/rss', TipoLinksFeed::PINTEREST => 'https://br.pinterest.com/seriesonlinepro/feed.rss',
                        TipoLinksFeed::WEEBLY=>'https://seriesonlinepro.weebly.com/series/feed',
                        TipoLinksFeed::WORDPRESS => 'https://seriesonline.pro/feed',
                    ],
                    'evernote' => '1AJyGmipUiuNNipTPfw9h5ea0kR74PLdV',
                    'drive' => ['1nEWtmw7Vwlyw6pVZDlrS7z4wWFRlwOOv'],
                    'twitter' => ['token' => '1258132448208211968-HtrlKHVGxA0d266QsYIfRN7Z4Gcgpv', 'secret' => '0tBnuJs7TRXgvZH6y8bR5FKfrFDhZvmOZDQMRHtVYv1gj'],
                    "twitter_link" => 'https://twitter.com/seriesonlinepro/status/',
                    'trello' => ["key" => "b97ae88abef70acbe6e082b03d887377", "token" => "4834c473e904e523494e9ca1744c18e65dcbf444712e6b47b1a0bc312496f86e", "quadro" => "5f2ccc80f6faf031b662def3"],
                    'BitLy'=>['key'=>'51ff6555e3a627459201ccc75a4bf083350dae50','group'=>'Bk8lgYaXN9y'],
                    'narro'=>'db37b3db-e16b-483b-9047-7fc33eec6829e0a85eb2-7bec-4677-8df3-ec065849c47c8a94d6bd-fef9-40be-9e4a-a293639ee3f8'
                ]
            ];
        }

        public function iniciar_processo()
        {

            try {
                $google_drive = new GoogleDrive();
                $cliente = new \GuzzleHttp\Client();
                $todos_os_links = [];
                foreach ($this->lista as $key => $value) {
                    $links_evernote_doc = $this->getLinksGoogleForEvernote($google_drive, $value['evernote']);
                    $links_evernote = $this->prepara_links_evernote($links_evernote_doc, $key);
//                    $links_nimbus = $this->getLinkNimbus($value['nimbus'], $key);
                    $this->preparaFeeds($value['feed'], $key);
                    $this->preparaTrello($value['trello'], $key);
                    $links_google = $this->getLinksGooglePathList($google_drive, $value["drive"], $key);
                    $links_twitter = $this->getLinksTwitter($value["twitter"], $key, $value['twitter_link'], 20);
                    $this->preparaBitLy($value['BitLy'],$key,$cliente);
//                    $this->getNarroLinks($value['narro'],$key,$cliente);
                }
                $this->addLinkNoBanco();
                $this->enviarLinks();
            } catch (\Throwable $ex) {
                \Log::error($ex);
            }
        }

        private function preparaBitLy(array $data, $type,\GuzzleHttp\Client $cliente,$limit = 100){
            try{
                    $this->getBitLy($type,$data['key'],$data['group'],$limit,$cliente);
            }catch (\Throwable $ex){
                Log::error($ex);
                Log::info("houve um erro com links bitly");
            }
        }

        private function getNarroLinks($token,$type, \GuzzleHttp\Client $cliente,$limit = 100){
            try{
                //https://www.narro.co/oauth2/authorize?client_id=c1552200-7ec7-4da4-941d-e5e6466a5cbc&response_type=code&redirect_uri=http://localhost/SyncWeb/public/narro/callback
                $response = $cliente->get("https://www.narro.co/api/v1/articles?limit=$limit",[
                    'headers'=>[
                        'Authorization'=>'Bearer '.$token,
                        'Accept'        => 'application/json'
                    ]
                ]);
                $resultado = json_decode($response->getBody()->getContents());
                foreach ($resultado->data as $link){
//                    $this->lista_links[] = ["link" => $link->location, "tipo" => $type, "titulo" => $link->title, "tipo_link" => TipoLinksFeed::NARRO];
                }
            }catch (\Throwable $ex){
                Log::error($ex);
                Log::info("houve um erro com links narro");
            }

        }

        private function getBitLy($type,$key,$group,$limit, \GuzzleHttp\Client $cliente){
            $response = $cliente->get("https://api-ssl.bitly.com/v4/groups/$group/bitlinks?size=$limit",[
                'headers'=>[
                    'Authorization'=>'Bearer '.$key,
                    'Accept'        => 'application/json'
                ]
            ]);
            $resultado = json_decode($response->getBody()->getContents());
            foreach ($resultado->links as $link){
                $this->lista_links[] = ["link" => $link->link, "tipo" => $type, "titulo" => $link->title, "tipo_link" => TipoLinksFeed::BITLY];
            }
        }

        private function preparaTrello(array $data, $type)
        {
            try {
                $client = new Client([
                    'key' => $data["key"],
                    'token' => $data["token"],
                ]);
                $boards = $client->getBoardCards($data["quadro"]);
                foreach ($boards as $board) {
                    $link = $board->url;
                    $titulo = substr($board->name,0,110);
                    $this->lista_links[] = ["link" => $link, "tipo" => $type, "titulo" => $titulo, "tipo_link" => TipoLinksFeed::TRELLO];
                }
            } catch (\Throwable $ex) {
                Log::error($ex);
                Log::info("Houve um erro com trello! Type" . $type);
            }
        }

        private function preparaFeeds(array $feeds, $type)
        {
            foreach ($feeds as $key => $value) {
                $this->getFeedLink($value, $type, $key);
            }
        }

        public function testeGoogleDrive()
        {
            $google = new GoogleDrive();
            dump($google->getLinksInPathWithTitle('1tDQUA-_yy5-CX6Y0AA8CcoE4SRkd51Vw'));
            dump($google);
        }

        private function getLinkNimbus($link_nimbus, $type)
        {
            $lista = [];
            try {
                $dom = HtmlDomParser::file_get_html($link_nimbus);
                $links = $dom->findMultiOrFalse("a.nns-item-note-link");
                if ($links != false) {
                    foreach ($links as $link_s) {
                        $link = "https://nimbusweb.me" . $link_s->getAttribute("href");
                        $titulo = $link_s->findOneOrFalse("span.nns-item-note-title")->text();
                        $lista[] = $link;
                        $this->lista_links[] = ["link" => $link, "tipo" => $type, "titulo" => $titulo, "tipo_link" => TipoLinksFeed::NIMBUS];
                    }
                }
            } catch (\Throwable $ex) {
                Log::error($ex);
                Log::info("houve um erro com nimbus: " . $link_nimbus . " erro: " . $ex->getMessage());
            }
            return $lista;
        }

        private function getLinksGooglePathList(GoogleDrive $google_drive, array $paths_id, $type)
        {
            $lista = [];
            foreach ($paths_id as $path) {
                $results = $google_drive->getLinksInPathWithTitle($path);
                foreach ($results as $result) {
                    $link = $result["link"];
                    $titulo = $result["title"];
                    $lista[] = $link;
                    $this->lista_links[] = ["link" => $link, "tipo" => $type, "titulo" => $titulo, "tipo_link" => TipoLinksFeed::GOOGLE_DRIVE];
                }

            }
            return $lista;
        }

        private function getLinksGoogleForEvernote(GoogleDrive $google_drive, $path_id)
        {
            return $google_drive->getLinksInPathWithTitle($path_id);
        }

        private function enviarLinks()
        {
            $links = ListFeed::whereStatus(FeedLinkStatus::NAO_ENVIADO)->get(['link']);
            $links_prontos = [];
            foreach ($links as $link) {
                $links_prontos[] = $link->link;
            }
            if (!empty($links_prontos)) {
                $this->enviarLinks_Api($links_prontos);
                ListFeed::whereStatus(FeedLinkStatus::NAO_ENVIADO)->update(["status" => FeedLinkStatus::ENVIADO]);
            }
        }

        private function enviarLinks_Api(array $links)
        {
            $apikey = '906f77JB9K7R5V99266dc4a4sBSmSC';
            $project = 'SYNC API';
            $drip = 'DRIP_DAYS';

            $urls = $links;
            $post = 'key=' . $apikey . '&project=' . $project . '&drip=' . $drip . '&urls=' . urlencode(implode('|', $urls));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, 'https://indexinject.com/api');
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 40);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            curl_close($ch);
        }


        private function getLinksTwitter($token_and_secret, $type, $link_twitter, $count = 20)
        {
            $links = [];
            try {
                Twitter::reconfig($token_and_secret);
                $json = Twitter::getHomeTimeline(['count' => $count, 'format' => 'json']);
                $resultados = json_decode($json);
                foreach ($resultados as $resultado) {
                    $link = $link_twitter . $resultado->id;
                    $titulo = $resultado->text;
                    $links[] = $link;
                    $this->lista_links[] = ["link" => $link, "tipo" => $type, "titulo" => $titulo, "tipo_link" => TipoLinksFeed::TWITTER];
                }
            } catch (\Throwable $ex) {
                Log::error($ex);
                Log::info("Houve um erro ao utilizar twitter");
            }
            return $links;
        }

        private function addLinkNoBanco()
        {
            foreach ($this->lista_links as $link) {
                ListFeed::firstOrCreate(["link" => $link["link"], "tipo" => $link["tipo"], "titulo" => $link["titulo"], "tipo_link" => $link["tipo_link"]]);
            }
        }

        private function prepara_links_evernote(array $links_evernote_doc, $type)
        {
            $links_evernote = [];
            foreach ($links_evernote_doc as $link_ever_doc) {
                $link = $this->getLinkEvernote($link_ever_doc["link"], $link_ever_doc["title"], $type);
                if ($link != false) {
                    $links_evernote[] = $link;
                }
            }
            return $links_evernote;
        }

        private function getLinkEvernote($link_evernote, $titulo, $type)
        {
            $re = '/evernote\.com\/shard(.*?)\"/';
            $dom = HtmlDomParser::file_get_html($link_evernote);
            $html = $dom->html();
            preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0);
            $link = false;
            if (isset($matches[0][1])) {
                $link = "https://www.evernote.com/shard" . $matches[0][1];
                $this->lista_links[] = ["link" => $link, "tipo" => $type, "titulo" => $titulo, "tipo_link" => TipoLinksFeed::EVERNOTE];
            }
            return $link;
        }

        private function getFeedLink($link, $type, $tipo_link)
        {
            $feed = FeedsFacade::make([$link]);
            $itens = $feed->get_items();
            $links = [];
            foreach ($itens as $item) {
                $l = $item->data["child"][""]["link"][0]["data"];
                $titulo = $item->data["child"][""]["title"][0]["data"];
                $links[] = $l;
                $this->lista_links[] = ["link" => $l, "tipo" => $type, "titulo" => $titulo, "tipo_link" => $tipo_link];
            }
            return $links;
        }

    }
