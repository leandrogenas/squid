<?php

namespace App\Http\Controllers;

use App\Enums\FeedLinkStatus;
use App\Enums\Sites;
use App\Models\animes\Animes;
use App\Models\animes\AnimesOnlineVip;
use App\Models\animes\AnimesVision;
use App\Models\animes\PostAnimesOrion;
use App\Models\animes\AnimePost;
use App\Models\animes\Utils\FazerPostagem;
use App\Models\animes\vip\PostVip;
use App\Models\CursoSEO;
use App\Models\feed\ListFeed;
use App\Models\feed\ProcessFeeds;
use App\Models\filmes\Bludv;
use App\Models\filmes\download\LinkFilme;
use App\Models\filmes\download\LinksDownloads;
use App\Models\filmes\Lapumia;
use App\Models\filmes\sites\ComandoTorrent;
use App\Models\filmes\TheMovieDB;
use App\Models\filmes\Utils\BuscaImagemOffline;
use App\Models\filmes\Utils\FuncoesUteisFilme;
use App\Models\filmes\Utils\PostFilmesVIP;
use App\Models\games\Skidrow;
use App\Models\games\Steam;
use App\Models\IA\VerificaPostagens;
use App\Models\Imagens;
use App\Models\IMDB;
use App\Models\series\BkSeries;
use App\Models\series\ConfigLinksDownload;
use App\Models\series\PublicaSerie;
use App\Models\wordpress\WordPress;
use App\Models\wordpress\WPress;
use App\Models\YouTube;
use App\Utils\FuncoesUteis;
use Corcel\Model\Post;
use DateTime;
use Evernote\Auth\OauthHandler;
use Evernote\Exception\AuthorizationDeniedException;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use HieuLe\WordpressXmlrpcClient\WordpressClient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Ixudra\Curl\Facades\Curl;
use Jenssegers\Date\Date;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\Process\Process;
use Thujohn\Twitter\Facades\Twitter;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDomBlank;
use voku\helper\SimpleHtmlDomNodeBlank;
use ImageOptimizer;
use willvincent\Feeds\Facades\FeedsFacade;

class TestController extends Controller
{

    public function testes(Request $request)
    {
//        $date = 'Hello World';
//        $tr = new GoogleTranslate("en");
//        $tr->setTarget("pt-br");
//        $data_ingles = $tr->translate($date);
//        dump($data_ingles);
//        echo date('Y/m/d', strtotime($data_ingles));
//        $json_dado = HtmlDomParser::file_get_html("https://api.themoviedb.org/3/movie/634904/credits?api_key=7d081946b53653ba225dd7f2b886d6de&language=pt-BR")->text();
//        $dado = json_decode($json_dado);
//        $texto = "";
//        $count = 0;
//        foreach ($dado->cast as $cast){
//            $texto .= "[";
//            $texto .= $cast->profile_path;
//            $texto .= ";".$cast->character."]";
//            $count++;
//            if($count == 10){
//                break;
//            }
//        }
//        dump($texto);
//        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
//        date_default_timezone_set('America/Sao_Paulo');
//        dump(strftime('%d de %B de %Y', strtotime('2013-03-31')));
//        $date = new Date("2013-03-31",'Europe/Brussels');
//        dump($date->format('j F Y'));
//        dump(Date::createFromFormat("Y-d-m","2013-03-31","Europe/Brussels")->format('j F Y'));
//            $link = "https://servidoralternativo.online/assistironline/zatch-bell/zatch-bell-episodio-53.mp4";
//            $headers = get_headers($link);
//            $mp4 = trim(substr($headers[5], 10));
//            dump($headers);
//            dump($mp4);
//            $duracao = "01:23:47";
//            dump(ltrim($duracao, '0:'));
//            $videoList = \Alaouy\Youtube\Facades\Youtube::searchVideos('Naruto Trailer');
//            dump($videoList[0]->id->videoId);
//            $titulo = "Cursed 1° Temporada Completa Torrent 2020 Legendado – Download";
//             dump(FuncoesUteis::identificar_temporada_serie($titulo));
//            $texto = "Episódio 19HDTV";
//            dump(Str::contains($texto," e"));
//            if($request->has('teste')){
//                $html = $request->post('html');
//                $link = "https://comandotorrents.org/wandavision-1a-temporada-torrent";
//                $link_the_movie = "85271";
//                $link_imdb = "tt9140560";
//                $comando = new ComandoTorrent($link);
//                $comando->is_serie = true;
//                $themovie = new TheMovieDB($link_the_movie, true);
//                $imdb = new IMDB($link_imdb);
//                $comando->theMovieDB = $themovie;
//                $comando->imdb = $imdb;
//                $comando->carregar_dados_html($html);
//                dump($comando);
//                dump($html);
//            }else{
//              return view("teste");
//            }
//            return $this->teste_comando_filmes();
        return $this->testeAnimesVision();
//            ListFeed::whereStatus(FeedLinkStatus::NAO_ENVIADO)->update(["status" => FeedLinkStatus::ENVIADO]);
//            return "OK";
//            $data = Carbon::now();
//            dump($data->format("d/m/Y"));
//            $data->addDays(5);
//            dump($data->format("d/m/Y"));
//       echo json_encode(FuncoesUteis::procurar_ID_postagem("Capitã Marvel"));
    }

    private function darPermissaoAdmin()
    {
        auth()->user()->assignRole('super-admin');
    }

    private function testeFirebaseMsn()
    {
        $enviador = Firebase::messaging();
        $mensagem = CloudMessage::withTarget('topic', '164520')->withNotification(['title'=>'Radiant 2 Dublado','body'=>'Teste com imagem','image'=>'https://animesonline.vip/wp-content/uploads/2021/02/Radiant-2-Dublado-episodio-3-animesonlinevip.jpeg']);
        dump($enviador->send($mensagem));
    }

    private function testeBitLy()
    {
        $cliente = new Client();
        $response = $cliente->get("https://www.narro.co/api/v1/articles?limit=?size=100", [
            'headers' => [
                'Authorization' => 'Bearer db37b3db-e16b-483b-9047-7fc33eec6829e0a85eb2-7bec-4677-8df3-ec065849c47c8a94d6bd-fef9-40be-9e4a-a293639ee3f8',
                'Accept' => 'application/json'
            ]
        ]);
        $result = json_decode($response->getBody()->getContents());
        dump($result);
    }

    private function testeCookie()
    {
        $response = Curl::to("https://animesvision.biz/animes/pokemon-twilight-wings/episodio-07/legendado/download")
            ->withHeader("Cookie: _ga=GA1.2.105183527.1590599211; __dtsu=51A01588707642B809B3EEE47BCE3508; __cfduid=d8b5eed374349babb75c628346a4392a51595708033; _gid=GA1.2.1050363194.1597025770; 494668b4c0ef4d25bda4e75c27de2817=6182c4f1-7fcc-43ea-9b6f-79ae9fda6445%3A1%3A2; trc_cookie_storage=taboola%2520global%253Auser-id%3D81cf79d7-ccad-4ba5-b2ec-77d5df21ab31-tuct5ab8f3f; a=ItGLCM8TlacyzdS2a15xngknzJz8wGeX; 6US=1; dom3ic8zudi28v8lr6fgphwffqoz0j6c=6182c4f1-7fcc-43ea-9b6f-79ae9fda6445%3A1%3A2; token_QpUJAAAAAAAAGu98Hdz1l_lcSZ2rY60Ajjk9U1c=BAYAXzCt6gFfMLEygAGBAsAAIMlA0yaBfNz8H4ImrzKjvb19rkkxmReZyMUYhTi5DkwBwQBHMEUCIQC19LuilzG307YMha3Ar4US-EA7CnAREbW7d71Jvwid9QIgIYztxEyr_PolhqrEFtcZyzNP6q3UgKymOQVVFDyecFo; XSRF-TOKEN=eyJpdiI6Ik1sUjBuNjZMQ1BTOUdWdWNaYllzc0E9PSIsInZhbHVlIjoiQmZCaG5uTUZtcW1jWC9VWGZKWTIrOThNdGNkMElaQlVta3JXN1pGYStBVE5HZUZBb21rVUR5VTh3bWQ3RmwzQSIsIm1hYyI6ImQ0MTZiODM2NjI5N2E4YWJiNjkxNTQ0ODZmMTc4NDhiNjk4MGZkZjE2NjRjOWZlMTA5NWI3NmUzZDkzNTc2OGMifQ%3D%3D; animes_vision_session=eyJpdiI6ImwyWlN4RzVOTG8yTVJwZXVIVmwyVlE9PSIsInZhbHVlIjoialZTYVFSN1IwRzBPMzhtTGFUOFJUbUhRblQwMU9Cc01HN3RZTENmMHN0YS9UellYd2tvaFBzYnVTbmpJa0Q5YSIsIm1hYyI6IjNlMTVmYzg1NDQ1YjY0OTdhODRjMTRmNTliMmZiMGU4OTg4ZWE5YjFkNjJkYzkwMGQ0ZWViM2ZiODZlZjk1NGMifQ%3D%3D")->withHeader("Accept-Language: pt-BR")->get();
        echo "<textarea>" . $response . "</textarea>";
    }

    private function testeTrello()
    {
//            $cliente = new \Trello\Client();
//            $cliente->authenticate('b97ae88abef70acbe6e082b03d887377', '4834c473e904e523494e9ca1744c18e65dcbf444712e6b47b1a0bc312496f86e', \Trello\Client::AUTH_URL_CLIENT_ID);
//            $boards = $cliente->cards()->getList('5f2dc86d7ca30d4c6350daa8');
//            dump($boards);
        $tokens = [
            "filmesviatorrents.biz" => ["key" => "1cc33f1771b68564a0326d53f35dcd02", "token" => "a91c13f38c5604f3b4c79419e6d72856bbc93a437d4de9de5d7bab3ff98042bf", "quadro" => "5f31a7e56337de77f8228e18"],
            "seriesonlinepro" => ["key" => "b97ae88abef70acbe6e082b03d887377", "token" => "4834c473e904e523494e9ca1744c18e65dcbf444712e6b47b1a0bc312496f86e", "quadro" => "5f2ccc80f6faf031b662def3"],
            "jogostorrent" => ["key" => "ae25cd47e894feea61c42e2ffec59863", "token" => "d85ce4f97fa3394a6b164df00df67b6542f987fc92f8bbff73df4991e901a9ad", "quadro" => "5f31a4891d76a939a8b25f04"],
            "filmestorrentvip" => ["key" => "d081814d45e16fec18893c2127fbeea0", "token" => "15f1b8e523727fa2b3e8ff72124786ecd7861789422132c2e03202293d7af081", "quadro" => "5f31ab8b72b4c525f1ad32b8"],
            "animesonline" => ["key" => "31837f5685fdd47710d9dd4cd9db7076", "token" => "910d2928cd74326dfcb9cdff5a287e6626b2949a7a79c5735104d3162fda6b92", "quadro" => "5d929b49569ec07d40a4f688"]
        ];
        $client = new \Stevenmaguire\Services\Trello\Client([
            'key' => $tokens['animesonline']["key"],
            'token' => $tokens['animesonline']["token"],
        ]);
        $boards = $client->getBoardCards($tokens['filmesviatorrents.biz']["quadro"]);
//            foreach ($boards as $board){
//                dump($board->url);
//            }
        dump($boards);
        dump($client->getCurrentUserBoards());
    }

    public function testeFilmesTorrentVIP()
    {
        $post = PostFilmesVIP::newest()->first();
        $content = $post->post_content;
        $dom = HtmlDomParser::str_get_html($content);
        $link = $dom->findOneOrFalse("a[href*='imdb']");
        if ($link != false) {
            $link_imdb = $link->getAttribute("href");
            $re = '/title\/(.*)\/|title\/(.*)/m';
            preg_match_all($re, $link_imdb, $matches);
            $imdb_id = empty($matches[1][0]) ? $matches[2][0] : $matches[1][0];
            $imdb = new IMDB($imdb_id);
            $imdb->pegar_dados_filme();
            dump($imdb);
        } else {
            dump("deu false");
        }
    }

    private function testeRegexEpisodioDownload()
    {
        $link = "https://down3.animesvision.com.br/freevision/VtAxr749ELkPFxS-SxX06w/1596565567/dwVkooRkQ1gjAxn7m3re/2/M/Muhyo_to_Rouji_no_Mahouritsu_Soudan_Jimusho_2/480p/AnV-05.mp4";
        $resultado = preg_replace('/down(.)/m', 'down6', $link);
        dump($resultado);
    }

    private function testeFeedBanco()
    {
        $data = Carbon::now()->subDays(30);
        dump($data->format("d/m/Y"));
        $links = ListFeed::where("created_at", "<=", $data)->get();
        dump($links);
    }

    private function testeIndex()
    {
        set_time_limit(0);
        $process = new ProcessFeeds();
        $process->iniciar_processo();
//            $process->testeGoogleDrive();
        dump($process);
    }

    private function testeEvernote()
    {

        $oauth_handler = new OauthHandler(false);

        $key = 'testeevernote';
        $secret = '07037f83ae623639';
        $callback = 'http://localhost/SyncWeb/public/login/evernote/callback';
        try {
            $oauth_data = $oauth_handler->authorize($key, $secret, $callback);
            dump($oauth_data);
        } catch (AuthorizationDeniedException $e) {
            echo "erro: " . $e->getMessage();
        }

    }

    private function testeIndexApi()
    {
        $apikey = '906f77JB9K7R5V99266dc4a4sBSmSC';
        $project = 'SYNC API';
        $drip = 'DRIP_DAYS';

        $urls = array('https://nimbusweb.me/s/share/4454920/ylvq3nxld7x6fqcqq6u1/J8wKwV8lDHpf8uww', 'https://nimbusweb.me/s/share/4454920/ylvq3nxld7x6fqcqq6u1/XxdbB0VHteTb9U3J');
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
        dump($response);
    }

    private function testeAnimesVip()
    {
//            $id = "816";
//            $episodios = PostVip::published()->type("episodio")->hasMeta("episodio_anime",$id)->limit(10)->get(['ID'])->sortBy(function ($episodio, $key) {
//                return $episodio->meta->episodio_ep;
//            });
//            foreach ($episodios as $episodio){
//                dump($episodio);
//            }
//            $animes = PostVip::published()->type("post")->limit(10)->get();
//            dump($animes);
        dump($this->get_web_page("https://animesonline.vip/"));
    }

    private function testeNimbusweb()
    {
        $link = "https://nimbusweb.me/s/share/4454920/ylvq3nxld7x6fqcqq6u1";
        $dom = HtmlDomParser::file_get_html($link);
        $links = $dom->findMultiOrFalse("a.nns-item-note-link");
        if ($links != false) {
            foreach ($links as $link_s) {
                dump($link_s->findOneOrFalse("span.nns-item-note-title")->text());
                dump("https://nimbusweb.me" . $link_s->getAttribute("href"));
            }
        }
    }

    private function testeTwitter()
    {
        $type = "_ANIME";
//            Twitter::reconfig(["consumer_key" => env("TWITTER_CONSUMER_KEY$type"),
//                "consumer_secret" => env("TWITTER_CONSUMER_SECRET$type"),
//                "token" => env("TWITTER_ACCESS_TOKEN$type"),
//                "secret" => env("TWITTER_ACCESS_TOKEN_SECRET$type"),]);
//            Twitter::reconfig(['token' => '1236170400268193792-NQUb8dh39rbo3daatkiVQ7742lJ7tm', 'secret' => 'hFfeBF459LaUMnSwCNJVZ1xjDRBDizzRY9PBONYAg6dzH']);
//            Twitter::reconfig(['token' => '1197323066965200897-qs0QIksCXrNgOFV5LzBLYNJZXkqYRv', 'secret' => 'v5tZaMJJi7uNBn2ZqS95yjFtmrY903FTe1TsjEf6rA8cn']);
//            Twitter::reconfig(['token' => '1198719495394775047-DnWQlMTSNVgVgUarRbvwLkdU0zMuGa', 'secret' => 'IJUrqJtGZs0z8ITeydhF8tcA1xqmTtuYCLIYclCrUKxgU']);
        Twitter::reconfig(['token' => '1258132448208211968-HtrlKHVGxA0d266QsYIfRN7Z4Gcgpv', 'secret' => '0tBnuJs7TRXgvZH6y8bR5FKfrFDhZvmOZDQMRHtVYv1gj']);
        $json = Twitter::getHomeTimeline(['count' => 5, 'format' => 'json']);
        $resultados = json_decode($json);
        dump($resultados[0]);
//            $links = [];
//            foreach ($resultados as $resultado) {
//                $links[] = "https://twitter.com/FilmesVia/status/" . $resultado->id;
//            }
//            dump($links);
    }

    private function testeGoogleDriveApi()
    {
        $client = new \Google_Client();
        $client->setApplicationName("My Project");
        $client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
        $client->setAuthConfig(Storage::path("client_id.json"));
        $client->setAccessType('offline');
        $tokenPath = 'token.json';
        if (Storage::exists($tokenPath)) {
            $accessToken = json_decode(Storage::get($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            dump("Eentrou aqui");
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
//                    $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode("4/2AEmPy6LNVVV_RQ7-nVXHJl2BSqXRmcTb-esOsM0oAtpbBTiZ7gaiSjfaysTyJxLnMRTKCK25NQ1Tv7JjgN1wqs");
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
//                if (!file_exists(dirname($tokenPath))) {
//                    mkdir(dirname($tokenPath), 0700, true);
//                }
//                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            Storage::put($tokenPath, json_encode($client->getAccessToken()));
        }
        $service = new Google_Service_Drive($client);

// Print the names and IDs for up to 10 files.
//            $optParams = array(
//                'pageSize' => 10,
//                'fields' => 'nextPageToken, files(id, name)'
//            );
//            $results = $service->files->listFiles($optParams);
//
//            if (count($results->getFiles()) == 0) {
//                print "No files found.\n";
//            } else {
//                print "Files:\n";
//                foreach ($results->getFiles() as $file) {
//                    dump($file->getName());
//                }
//            }
        $id = "root";
        $query = "'1tDQUA-_yy5-CX6Y0AA8CcoE4SRkd51Vw' in parents";

        $optParams = [
            'fields' => 'files(id, name)',
            'q' => $query
        ];

        $results = $service->files->listFiles($optParams);
        dump(count($results->getFiles()));
        if (count($results->getFiles()) == 0) {
            print "No files found.\n";
        } else {
            print "Files:\n";
            foreach ($results->getFiles() as $file) {
                dump($file->getName(), $file->getID());
            }
        }

    }

    private function testeFeed()
    {
        $feed = FeedsFacade::make(['https://br.pinterest.com/animesonlinevip/feed.rss']);
//            dump($feed->get_items());
        $itens = $feed->get_items();
        $links = [];
        dump($itens[0]->data["child"][""]["title"][0]["data"]);
//            foreach ($itens as $item){
//                dump($item);
//               $links[] = $item->data["child"][""]["link"][0]["data"];
//            }
//            dump($links);
////            $data = array(
////                'title'     => $feed->get_title(),
////                'permalink' => $feed->get_permalink(),
////                'items'     => $feed->get_items(),
////            );
////                dump($data);
//            $re = '/evernote\.com\/shard(.*?)\"/';
//            $dom = HtmlDomParser::file_get_html("https://docs.google.com/document/d/18MNSOkAkDqJFzzZ7P3ArZajydOFsWNa20ShxFIM4Eh0/edit");
//            $html = $dom->html();
//            preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0);
//            dump($matches);
//            echo "<textarea>$html</textarea>";
//            $ids = [1,2];
//            ListFeed::whereIn("id",$ids)->update(["status"=>FeedLinkStatus::ENVIADO]);
    }

    private function testeOtimizarImagem()
    {
//            ImageOptimizer::optimize("img/teste.png");
//            return "ok";
//            dump(get_headers("https://seriesonline.pro/api/make/check/?list=0652cca7dbc3d2e7"));
//            $link = "https://seriesonline.pro/api/make/check/?list=0652cca7dbc3d2e7";
//            $headers = get_headers($link);
//            $m_array = preg_grep('/(.)googlevideo(.)/', $headers);
//            $location = trim(str_replace("Location:","",array_values($m_array)[0]));
//            dump($location);
    }

    private function testeIA()
    {
        $verifica = new VerificaPostagens();
        $result = $verifica->verificar_filmes();
        dump($result);
    }

    public function testeAnimesVision()
    {
//            $link_normal = "https://animesvision.biz/animes/steven-universo-futuro-dublado";
//            $this->getHtmlAnimesVision($link_normal);
//            $link_episodio = "https://animesvision.biz/animes/steven-universo-futuro-dublado/episodio-01/dublado";
//            dump(explode("/episo",$link_normal));
//            dump(explode("/episo",$link_episodio));
//            dump(HtmlDomParser::file_get_html($link_normal));
//            dump(AnimesOnlineVip::procurar_anime("naruto"));
//            $wp = new WPress();
//            dump($wp->getPostPorSite(Sites::ANIMESONLINE_VIP,94148));
//            $str = '1 hr 54 min';
//            preg_match_all('!\d+!', $str, $matches);
//            print_r($matches);
//            set_time_limit(0);
//            $before = microtime(true);
//            dump(HtmlDomParser::file_get_html("https://animesvision.biz/animes/quanzhi-fashi-4/episodio-01/legendado/download")->html());
        $anime = new AnimesVision("https://animesvision.biz/animes/yasuke-dublado");
        $anime->post_vip_id = 165280;
        $anime->carregar(6, 6);
        dump($anime);
       // $lista[] = $anime;
        //$postar = new FazerPostagem();
       // $postar->postar_animes($lista);
////            $after = microtime(true);
////            echo ($after - $before) . " sec\n";
//            dump($postar);
//            dump($anime);
//            FFMpeg::open('https://down1.animesvision.com.br/freevision/55Ny7y6opQ45bbsX9-6Pjg/1590623632/wu2LRzHQM6eA1pJ3Nlv2/2/T/Tamayomi/480p/AnV-09.mp4')
//                ->export()
//                ->onProgress(function ($percentage) {
//                    echo "{$percentage}% transcoded";
//                });
//            $script = 'ffmpeg -i "https://down2.animesvision.com.br/freevision/4GefphKdqx_BmHJyWQK_yw/1590629946/cKFJgBkY97QP0HhX10Kb/2/A/A3_Season_Spring_Summer/1080p/AnV-08.mp4" 2>&1 | find "Duration"';
//            $p = new Process($script);
//            $p->run();
//            // executes after the command finishes
//            if (!$p->isSuccessful()) {
//               dump($p->getErrorOutput());
//            }
//
//            $resultado = $p->getOutput();
//           dump($resultado);
//           dump($p->isSuccessful());
//            $after = microtime(true);
//            echo ($after - $before) . " sec\n";
//           dump(Str::contains($resultado,"Output file is empty, nothing was encoded"));
//            $str = 'Episodio 01 [Especial 01] - ';
//            $str = preg_replace("/(\[.*\])/m","",$str);
//            preg_match("/Episodio(.*)/m",$str,$m);
//            dump($m);
//            $re = '!\d+!';
//            preg_match_all($re, $m[1], $matches);
//            dump($matches);
    }

    private function getCustomFieldValue(array $values, $key_search)
    {
        foreach ($values as $value) {
            if ($value["key"] === $key_search) {
                return $value;
            }
        }
        return [];
    }

    public function testeAnimes()
    {
        $wp = new WPress();
        $content = $wp->getPostPorSite(Sites::ANIMESONLINE_VIP, 94110);
        dump($content);
        $custom = $content["custom_fields"];
        dump($this->getCustomFieldValue($custom, "episodio_capa"));
//            $link = "https://animesonline.vip/africa-no-salaryman/";
//            $link = str_replace("/","",explode("animesonline.vip/",$link)[1]);
//            $post = Post::published()->type("post")->where("post_name",$link)->first();
//            $post = PostAnimesOrion::hasMeta("episodio_anime","3285")->count();
//        dump($post);
//        dump(PostAnimesOrion::published()->hasMeta("episodio_anime", "3285")->hasMeta("episodio_ep",1,">=")->hasMeta("episodio_ep",13,"<=")->get());
//        dump(AnimePost::buscaAnimeOrion("Majutsushi Orphen"));
//        $cliente = new Client();
//        $response = $cliente->request("POST","https://servidoralternativo.online/upload-punch.php",[
//            'form_params' => [
//                'user' => 'terminal',
//                'password' => 'nutela',
//                'anime'=>"testemaluco",
//                'episodio_numero' =>'1',
//                'link'=>'https://www.animeson.club/hack-roots/41554.mp4'
//            ]
//        ]);
//        $resultado = $response->getBody()->getContents();
//        $resultado = json_decode($resultado);
//        dump($resultado);
//        $wp = new WPress();
//        $anime = new AnimePost(86481,3300,"Mai-Otome",$wp);
//        $anime->start(2,3);
//        dump($anime->houve_erro);
//        return "Ok";
//        $location = get_headers("http://tudogostoso.blog/make/check/?list=2897c03bbb1ed961")[15];
//        dump(str_replace("Location: ","",$location));
//            $link_orion = "https://animesorion.co/ore-monogatari-legendado/";
//            $link = str_replace("/", "", explode("animesorion.co/", $link_orion)[1]);
//            $post = PostAnimesOrion::published()->type("post")->where("post_name", $link)->first();
//            dump($post->ID);
//            /**@var Carbon $data*/
//            $data = $post->post_date;
//            $dia_aleatorio  = random_int(1,10);
//            $data_final = $data->addDays($dia_aleatorio);
//            dump($data_final);
//            dump($dia_aleatorio);
//            dump(Carbon::createFromFormat("Y-m-d H:i:s",$post->post_date));
//            $anime = new AnimesVision("");
//            $anime->carregar_orion(3974);
//            dump($anime);
//            dump(PostAnimesOrion::find(144359));
//            $headers = get_headers("http://tudogostoso.blog/make/check/?list=18476c2ab93ce545");
//            dump($headers);
//            $result = array_search("Location",$headers);
//            dump($result);
//            $array = array(0 => 'blue', 1 => 'red', 2 => 'green string', 3 => 'red');
//            $m_array = preg_grep('/(.*)googlevideo(.*)/', $headers);
//            dump(trim(str_replace("Location:","",array_values($m_array)[0])));
//            dump(Post::find(72176));
//           $post_orion = PostAnimesOrion::find(144420);
//            dump(PostAnimesOrion::published()->type("post")->where("ID",">=",5820)->where("ID","<=",5830)->get());
//            dump($post_orion);
//            $title = trim(FuncoesUteis::multipleReplace(["Legendado","Dublado"],"",$post_orion->post_title));
//            dump($title);
//            dump(Post::published()->where("post_title","like",$title."%")->exists());
//            dump("Ö");
//            $anime = new AnimesVision("");
//            $anime->carregar_orion(3792,1,1);
//            $anime->baixarImagem();
//            dump($anime);
//            dump(PostAnimesOrion::find(4495));
    }

    private function testeImagensOffline()
    {
        $link = "https://www.imdb.com/title/tt7201558/";
        dump(FuncoesUteis::pegarIDLinkIMDB($link));
//        set_time_limit(0);
//        $b = new BuscaImagemOffline(200,202);
//        dump($b->start());
    }

    private function testeFilmesBiz()
    {
//            $endpoint = "https://filmesviatorrents.biz/xmlrpc.php";
        $endpoint = "https://www.kfilmestorrent.com/xmlrpc.php";

# Create client instance
        $wpClient = new WordpressClient();

# Set the credentials for the next requests
        $wpClient->setCredentials($endpoint, 'sync', '$HGwD8#hch@mWWOD3q3a&xsB');
//            $path_imagem = "../public/img/img-padrao.png";
//            $mime = 'image/png';
//            $data = file_get_contents($path_imagem);
//            //10272
//            dump($wpClient->uploadFile("img-padrao.png", $mime, $data));
        dump($wpClient->getPost(7335));

//        dump($data_atualizada);
//        $content = ['post_date'=>$wpClient->createXMLRPCDateTime(Carbon::now()->toDateTime())];
//        dump($content);
//        dump($wpClient->editPost($post_id,$content));
//        $dados = $wpClient->getPost($post_id);
//        dump($dados);
//        $content["custom_fields"] = ["key" => "episodios_links", "value" => "teste"];
//        dump($content);
//        dump($wpClient->editPost($post_id, $content));
//        dump($wpClient->getPost($post_id));
//        $dom = HtmlDomParser::str_get_html($dados["post_content"]);
//        dump($dom->findOne("img")->getAttribute("src"));
    }

    private function testeJogos()
    {
//        $endpoint = "https://jogostorrents.site/xmlrpc.php";
//
//# Create client instance
//        $wpClient = new WordpressClient();
//
//# Set the credentials for the next requests
//        $wpClient->setCredentials($endpoint, 'pirateflow', 'sc0rp1ion');
//        $post_id = 14684;
//        $imagem = "../storage/app/public/teste.png";
//        $mime = 'image/png';
//        $data = file_get_contents($imagem);
//        dump($wpClient->uploadFile("sounlindo.png", $mime, $data, false));
//        dump($wpClient->getPost($post_id));
//        testar cookie steam
//        $response = Curl::to("https://store.steampowered.com/app/1147480/WARRIORS_OROCHI_4_The_Ultimate_Upgrade_Pack/")
//            ->withHeader("Cookie: birthtime=-127166399;lastagecheckage=21-0-1966")->withHeader("Accept-Language: pt-BR")->get();
//        $dom = HtmlDomParser::str_get_html($response);
//        $tradutor = new GoogleTranslate();
//        $texto = $dom->findOne("div#game_area_description")->text();
//        dump($tradutor->setTarget("pt")->translate(html_entity_decode($texto)));
        $link_skidrow = "https://www.skidrowreloaded.com/corruption-2029-codex/";
        $dom = HtmlDomParser::file_get_html($link_skidrow);
        $p = $dom->findMulti("p");
        $links_download = [];
        $foi_uma_vez = false;
        foreach ($p as $e) {
//            dump($e->html());
            $link_down = [];
            $link_multi = [];
            if (count($e->getElementsByTagName("span")) == 1 && count($e->getElementsByTagName("a")) == 1) {
                $href = $e->getElementByTagName("a")->getAttribute("href");
                $t = $e->getElementByTagName("span")->text();
                if (!$this->verificar_links_e_texto($href, $t)) {
                    $link_down["texto"] = $t;
                    $link_down["link"] = $href;
                    $links_download[] = $link_down;
                }
//                dump("entrou no primeiro if");
//                dump($links_download);
            } else if (count($e->getElementsByTagName("span")) > 2 && count($e->getElementsByTagName("a")) > 1) {
                foreach ($e->getElementsByTagName("span") as $span) {
                    if ($span->getAttribute("style") == "color: #ecf22e;") {
                        if ($foi_uma_vez) {
                            $link_down["multi"] = $link_multi;
                            $links_download[] = $link_down;
                            $link_multi = [];
                        }
                        $link_down["texto"] = $span->text();
                        $foi_uma_vez = true;
                    }
                    try {
                        $proximo = $span->nextSibling();
                        while (true) {
                            if ($proximo->tag == "a") {
                                $links["texto"] = $proximo->text();
                                $links["link"] = $proximo->getAttribute("href");
                                $link_multi[] = $links;
                            } else if ($proximo->tag == "span") {
                                if ($proximo->getAttribute("style") == "color: #00ff00;") {
                                    break;
                                }
                            }
                            $proximo = $proximo->nextSibling();
                        }
                    } catch (\Throwable $ex) {

                    }
                }
                dump("entrou no penultimo if");

            } else if (count($e->getElementsByTagName("a")) > 1) {
                $link_down = [];
                $span = $e->getElementByTagName("span");
                $link_down["texto"] = $span->text();
                $links = [];
                foreach ($e->getElementsByTagName("a") as $a) {
                    if (!$this->verificar_links_e_texto($span->text())) {
                        $href = $a->getAttribute("href");
                        $t = $a->text();
                        $l["texto"] = $t;
                        $l["link"] = $href;
                        $links[] = $l;
                    }
                }
                $link_down["multi"] = $links;
                $links_download[] = $link_down;
                dump("entrou no ultimo if");

            }
        }
        dump($links_download);
//        $steam = new Steam("https://store.steampowered.com/app/635250/Evil_Genome/");
//        $steam->carregar_dados();
//        dump($steam);
//        $skidrown = new Skidrow("https://www.skidrowreloaded.com/sd-gundam-g-generation-cross-rays-codex/");
//        $skidrown->is_pt_br = false;
//        $skidrown->carregar_dados();
//        dump($skidrown);
    }

    private function verificar_links_e_texto($link, $texto = "")
    {
        $links = explode(",", \Config::get("sync.ignora_links_jogo"));
        foreach ($links as $l) {
            if (Str::contains($link, $l) || Str::contains($texto, $l)) {
                return true;
            }
        }
        return false;
    }

    public function lapumiaDados()
    {
        set_time_limit(0);
        $link = "https://lapumia.org/jumanji-proxima-fase-torrent-2020-dublado-e-legendado/";
        $link_the_movie = "512200";
        $link_imdb = "tt7975244";
        $lapumia = new Lapumia($link);
        $themovie = new TheMovieDB($link_the_movie, false);
        $imdb = new IMDB($link_imdb);
        $lapumia->theMovieDB = $themovie;
        $lapumia->imdb = $imdb;
        $lapumia->carregar_dados();
//        $wordpress = new WordPress();
//        $resultado = $wordpress->uploadImagem(Sites::FILMESVIATORRENT_INFO, $lapumia->img_site);
//        $lapumia->img_url_upload = $resultado["url"];
        $lapumia->gera_code_por_site(Sites::FILMESTORRENT_VIP);
//        dump($wordpress->addPostPorSite(Sites::FILMESVIATORRENT_BIZ, $lapumia));
        dump($lapumia->custom_content);
    }

    private function bludvSerie()
    {
        set_time_limit(0);
        $link = "https://comandotorrentshd.tv/misterios-sem-solucao-2a-temporada-completa-torrent-2020-dual-audio-dublado-web-dl-720p-download/";
        $link_the_movie = "105177";
        $link_imdb = "tt11666848";
        $bludv = new Bludv($link);
        $themovie = new TheMovieDB($link_the_movie, true);
        $imdb = new IMDB($link_imdb);
        $bludv->theMovieDB = $themovie;
        $bludv->imdb = $imdb;
        $bludv->is_serie = true;
        $bludv->carregar_dados();
        dump($bludv);
//            $bludv->preparar_imagens_por_site(Sites::FILMESVIATORRENT_INFO);
//            $wordpress = new WordPress();
//            $resultado = $wordpress->uploadImagem(Sites::FILMESVIATORRENT_INFO,
//                $bludv->get_imagem_por_site(Sites::FILMESVIATORRENT_INFO));
//            $bludv->img_url_upload = $resultado["url"];
//            $bludv->gera_code_por_site(Sites::FILMESVIATORRENT_INFO);
//        dump($wordpress->addPostPorSite(Sites::FILMESVIATORRENT_INFO, $bludv));
//        dump($bludv->content);
    }

    private function testeLapumiaDownload()
    {
        $link = "https://torrentdosfilmes.top/?p=24420";
        $dom = HtmlDomParser::file_get_html($link);
        $spans = $dom->find("h2:contains(Versão)");
        dump(count($spans));
        $links_de_download = [];
        $spans_adicionados = [];
        foreach ($spans as $span) {
            $p = $span->nextSibling();
            dump($p->html());
//            dump($p->nextSibling()->html());
            $a_links = $p->nextSibling()->find("a");
//                dump($a_links);
//                dump($a_links->html());
            $lapumia_link = new LinksDownloads();
            $lapumia_link->texto_links = $span->text();
            $audio_identificado = $span->text();
            $links_filmes = [];
            $spans_adicionados[] = $span->text();
            dump(count($a_links));
            foreach ($a_links as $link_a) {
                $link_attr = $link_a->getAttribute("href");
                dump($link_attr);
                if (Str::contains($link_attr, "magnet:?")) {
                    $link_magnetico = $link_attr;
                    $link_filme = new LinkFilme();
                    $qualidade_link = $link_a->getElementByTagName("img")->getAttribute("src");
                    $link_filme->link = $link_magnetico;
                    $link_filme->qualidade_link = $qualidade_link;
                    $link_filme->audio_link = $audio_identificado;
                    $links_filmes[] = $link_filme;
                    $links_adicionados[] = $link_magnetico;
                }
            }
            $lapumia_link->links = $links_filmes;
            $links_de_download[] = $lapumia_link;
        }
        $this->checkSpans($dom, "h2:contains(FULL)", $links_de_download, $spans_adicionados);
        $this->checkSpans($dom, "h2:contains(Dual)", $links_de_download, $spans_adicionados);
        dump($links_de_download);
    }

    private function checkSpans(HtmlDomParser $dom, $selector, array &$links_de_download, array &$spans_adicionados)
    {
        $spans = $dom->find($selector);
        foreach ($spans as $span) {
            $p = $span->nextSibling();
            if (!in_array($span->text(), $spans_adicionados)) {
                $a_links = $p->find("a");
                if (count($a_links) > 0) {
                    $lapumia_link = new LinksDownloads();
                    $lapumia_link->texto_links = $span->text();
                    $audio_identificado = $span->text();
                    $links_filmes = [];
                    $spans_adicionados[] = $span->text();
                    foreach ($a_links as $link_a) {
                        $link_attr = $link_a->getAttribute("href");
                        if (Str::contains($link_attr, "magnet:?")) {
                            $link_magnetico = $link_attr;
                            $link_filme = new LinkFilme();
                            $qualidade_link = $link_a->getElementByTagName("img")->getAttribute("src");
                            $link_filme->link = $link_magnetico;
                            $link_filme->qualidade_link = $qualidade_link;
                            $link_filme->audio_link = $audio_identificado;
                            $links_filmes[] = $link_filme;
                        }
                    }
                    $lapumia_link->links = $links_filmes;
                    $links_de_download[] = $lapumia_link;
                }
            }
        }
    }

    public function testa_imagem()
    {
        $imagemLogo = public_path('img\padrao-filmesvip.png');
        $imagemTeste = public_path('img\temp.png');
        $img = new ImageManager(array('driver' => 'gd'));
        $img_logo = $img->make($imagemLogo);
        $img = $img->make($imagemTeste);
        $img->resize(352, 407);
//        $img->insert($imagemLogo,"center");
        $img->save();
        $img_logo->insert($imagemTeste, 'center');
        $img_logo->insert($imagemLogo, 'center')->save(public_path('img\vai.png'));
//        $url = "https://image.tmdb.org/t/p/w600_and_h900_bestv2/tX0o4AdHpidgniTWwfzK0dNTKrc.jpg";
//        $contents = file_get_contents($url);
//        $name = substr($url, strrpos($url, '/') + 1);
//        Storage::put($name, $contents);
//        echo "foi";
    }

    private function teste_imdb()
    {
        $im = new IMDB("tt7349896");
        $im->pegar_dados_filme();
        dump($im);
    }

    public function testeTheMovie()
    {
//        return TheMovieDB::procurar_filme("xmen primeira classe");
//        return IMDB::procurar_filme("xmen primeira classe");
//        $link = "https://www.bludv.tv/o-labirinto-do-fauno-el-laberinto-del-fauno-torrent-2006-dublado-dual-audio-legendado-2160p-4k-download/";
//        return FuncoesUteis::pegar_titulo_filme($link);
//        $tm = new TheMovieDB("https://www.themoviedb.org/movie/580600-terra-willy?language=pt-BR");
//        $imdb = new IMDB("https://www.imdb.com/title/tt7049682/?ref_=nv_sr_1?ref_=nv_sr_1");
//        $imdb->pegar_dados_serie();
//        $tm->pegar_dados();
//        dump($imdb);
//        $d = TheMovieDB::procurar_filme("o herdeiro das drogas");
//        dump($d);
        $tv = new TheMovieDB("320288", false);
        $tv->pegar_dados();
        dump($tv->getCastDirectorArray());
    }

    private function getCurlWithCookie($url, $client = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $client);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'C:\xampp\htdocs\SyncWeb\cookie.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'C:\xampp\htdocs\SyncWeb\cookie.txt');
        $response = curl_exec($curl);
        curl_close($curl);
//            $options = array(
//                CURLOPT_RETURNTRANSFER => true,   // return web page
//                CURLOPT_HEADER => true,  // don't return headers
//                CURLOPT_FOLLOWLOCATION => true,   // follow redirects
//                CURLOPT_MAXREDIRS => 10,     // stop after 10 redirects
//                CURLOPT_ENCODING => "",     // handle compressed
//                CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36", // name of client
//                CURLOPT_AUTOREFERER => true,   // set referrer on redirect
//                CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
//                CURLOPT_TIMEOUT => 120,    // time-out on response
//                CURLOPT_NOBODY => true,
//            );
//
//            $ch = curl_init($url);
//            $response = curl_setopt_array($ch, $options);
        return $response;
    }

    public function pegar_dados_curso()
    {
//            $url = "https://vip.seodeverdade.pro/login-2/";
//            $headers = ['Referer' => 'https://vip.seodeverdade.pro'];
//            $cliente = new Client([
//                "headers"=>$headers
//            ]);
////            $cliente = new Client();
//            $cookieJar = CookieJar::fromArray([
//                'wordpress_logged_in_cd6291735c0896f7018ae40498adfd54' => 'Willian+Montanaro+Esteves+Araujo%7C1594302531%7C6Srh1nPgIPVAc64WYM5RVwI3aaM601E1zUAvnkEd92y%7Cff9d05fdce6f028c0a96578ecb9541ee16a7c9591344674d644afe668ef69eae'
//            ], 'vip.seodeverdade.pro');

//            $response = $cliente->request("POST", $url, [
//                'form_params' => [
//                    "log" => "carromotorsad@gmail.com",
//                    "pwd" => "6x3dc91Pb@ce",
//                    "wp-submit" => "Log In",
//                    "redirect_to" => "https://vip.seodeverdade.pro/painel-vip/",
//                    "mepr_process_login_form" => "true",
//                    "mepr_is_login_page"=>"true"
//                ],
//            ]);
//            $response = $cliente->request("GET","https://vip.seodeverdade.pro/courses/seo-on-page/7929-6933",[
//                "cookies"=>$cookieJar
//            ]);
//            $response = $cliente->get("https://player.vimeo.com/video/240759751/config");
//            $resultado = $response->getBody()->getContents();
//            echo $resultado;
//            echo "<textarea>$resultado</textarea>";
//            $result = json_decode($resultado);
//          dump($result->request->files->progressive);
//          $qualidades = [1080,720,540,360];
//          $link_pronto = "";
//          foreach ($qualidades as $qualidade){
//              foreach ($result->request->files->progressive as $link){
//                  if($link->height >= $qualidade){
//                      $link_pronto = $link->url;
//                      break 2;
//                  }
//              }
//          }
//          dump($link_pronto);
        $curso = new CursoSEO("https://vip.seodeverdade.pro/courses/seo-on-page/7929-729", 'wordpress_logged_in_cd6291735c0896f7018ae40498adfd54', 'Willian+Montanaro+Esteves+Araujo%7C1594302531%7C6Srh1nPgIPVAc64WYM5RVwI3aaM601E1zUAvnkEd92y%7Cff9d05fdce6f028c0a96578ecb9541ee16a7c9591344674d644afe668ef69eae');
        $curso->carregar_paginas();
        dump($curso);
    }

    public function pagina_cursos()
    {
        return view("screens.cursos.pagina-curso");
    }

    public function carregar_dados_cursos(Request $request)
    {
        set_time_limit(0);
        $log = "";
        try {
            $link = $request->post("link");
            $cookie_name = $request->post("cookie_name");
            $cookie_value = $request->post("cookie_value");
            $curso = new CursoSEO($link, $cookie_name, $cookie_value);
            $curso->carregar_paginas();
            $jsonLinks = $curso->preparaJsonLinksDownload();
            $log = $curso->log;
            return json_encode(["erro" => true, "log" => $log, "resultado" => $jsonLinks]);
        } catch (\Throwable $ex) {
            \Log::error($ex);
            $log .= $ex->getMessage();
            return json_encode(["erro" => true, "log" => $log, "resultado" => ""]);
        }
    }

    public function pegar_dados_e_verificar(HtmlDomParser $dom, array $multiselector)
    {
        try {
            foreach ($multiselector as $selector) {
                $find = $dom->findOneOrFalse($selector);
                if ($find != false) {
                    return trim($find->nextSibling()->text());
                }
            }
            return "";
        } catch (\Throwable $ex) {
            \Log::error($ex);
            return "";
        }
    }

    private function pegarSpans(HtmlDomParser $dom)
    {
        $lista_span = [];
        $tags = ["span", "strong", "h2 > strong"];
        $selectors = [":contains('Versão')", ":contains('::')", ":contains('#8212;')"];
        foreach ($tags as $tag) {
            foreach ($selectors as $selector) {
                $css_seletor = $tag . $selector;
                $spans = $dom->findMultiOrFalse($css_seletor);
                if ($spans != false) {
                    foreach ($spans as $span) {
                        $lista_span[] = $span->text();
                    }
                }
            }
        }
        $spans = $dom->findMultiOrFalse("h2 > strong");
        if ($spans != false) {
            foreach ($spans as $span) {
                $lista_span[] = $span->text();
            }
        }
        return $lista_span;
    }

    private function testeSerie()
    {
//            $link_serie = "https://www.bludv.tv/dcs-legends-of-tomorrow-5a-temporada-completa-torrent-2020-dublado-dual-audio-legendado-web-dl-720p-e-1080p-e-2160p-4k-download/";
        $link_serie = "https://www.bludv.tv/the-resident-3a-temporada-completa-torrent-2019-dublado-dual-audio-legendado-web-dl-720p-e-1080p-e-2160p-4k-download/";
        $dom = HtmlDomParser::file_get_html($link_serie);
//            dump($dom->findOneOrFalse("strong:contains('Traduzido')")->nextSibling()->text());
//            dump($this->pegar_dados_e_verificar($dom,["strong:contains('Traduzido')"]));
//            dump(FuncoesUteis::pegar_titulo_filme($link_serie));
//            dump($dom->html());
//            dump($dom->findOneOrFalse("strong:contains('#8212;')"));
        $texto_lista_span = $this->pegarSpans($dom);
        dump($texto_lista_span);
//            $spans = $dom->findMultiOrFalse("span:contains('Versão')");
//            if ($spans == false) {
//                $check_spans = ["::", "#8212;"];
//                foreach ($check_spans as $check) {
//                    dump("entrou aqui kk");
//                    $spans = $dom->findMultiOrFalse("strong:contains('" . $check . "')");
//                    if ($spans != false) {
//                        dump($spans->text());
//                        break;
//                    }
//                }
//                if ($spans == false) {
//                    dump("chegou aqui");
//                    foreach ($check_spans as $check) {
//                        $spans = $dom->findMultiOrFalse("span:contains('" . $check . "')");
//                        if ($spans != false) {
//                            break;
//                        }
//                    }
//                }
//            }
//            if ($spans == false) {
//                $spans = $dom->findMultiOrFalse("h2 > strong");
//            }
//        $textos = $dom->find("span[style='color: #ff0000;']");
        $dados = [];
        $count_span = 0;
        $links = $dom->findMulti("a[href*='magnet']");
        $lista_episodio = "";
        $texto_anterior_div = "";
        $total_span = count($texto_lista_span);
        dump($total_span);
        foreach ($links as $link) {
            $texto_anterior = "|";
            $elemento_texto = $link->previousSibling();
            while (!Str::contains($texto_anterior, "Ep")) {
                try {
                    $texto_anterior = $elemento_texto->text();
                    $elemento_texto = $elemento_texto->previousSibling();
                } catch (\Throwable $ex) {
                    $texto_anterior = "Temporada Completa";
                    break;
                }
            }
            if (count($link->find("img")) > 0) {
                $qualidade = $this->identificar_qualidade_por_imagem($link->findOne("img")->getAttribute("src"));
                if ($qualidade == false) {
                    $p_com_texto = $link->parent()->previousSibling()->previousSibling();
                    $qualidade = $this->identificar_qualidade_por_imagem($p_com_texto->text());
                }
                $texto_div = $texto_anterior;
                $texto_link = $qualidade;
                $texto_para_verificar = $texto_div . $qualidade . $texto_link;
                if (Str::contains($texto_anterior_div, "Ep")) {
                    if ($total_span > ($count_span + 1)) {
                        $count_span++;
                    }
                }
            } else {
//                        dump($texto_anterior);
                $texto_div = $this->remover_texto_links($texto_anterior);
                $texto_link = $link->text();
                $texto_para_verificar = $texto_div . $texto_link;
                if (Str::contains($texto_para_verificar, " e")) {
                    $resultado = trim(preg_replace('/e.*/', '', $texto_para_verificar));
                    if (Str::contains($lista_episodio, $resultado)) {
                        $count_span++;
                        $lista_episodio = "";
                    }
                }
            }
//                dump("Lista Ep: ".$lista_episodio);
//                dump("Texto Verifica: ".$texto_para_verificar);
//                dump(Str::contains($texto_para_verificar,"ao"));
//                dump(Str::contains($lista_episodio, $texto_para_verificar));
//                dump(Str::contains($lista_episodio, $texto_para_verificar));
//                dump("Texto Verifica: " . $texto_para_verificar);
//                dump("Lista Episodio: " . $lista_episodio);
//                dump("Texto Div Anterior: " . $texto_anterior_div);
//                dump(Str::contains($texto_anterior_div, "Temporada") && !Str::contains($texto_para_verificar,
//                        "Temporada"));
//                dump("Contem?: ".Str::contains(" e ", $texto_para_verificar));
            if (Str::contains($lista_episodio, $texto_para_verificar)) {
                $count_span++;
                $lista_episodio = "";
            } elseif (Str::contains($texto_anterior_div, "Temporada") && !Str::contains($texto_para_verificar,
                    "Temporada")) {
                $count_span++;
                $lista_episodio = "";
            } elseif (Str::contains($texto_para_verificar,
                    "ao") && !Str::contains($lista_episodio, "ao")) {
                $remove_ao = trim(preg_replace('/ao.*/', '', $texto_para_verificar));
                if (Str::contains($lista_episodio, $remove_ao)) {
                    $count_span++;
                    $lista_episodio = "";
                }
            } elseif ($texto_anterior_div !== $texto_div) {
                if (Str::contains($lista_episodio, $texto_div)) {
                    $count_span++;
                    $lista_episodio = "";
                }
            }
//                dump($count_span);
            $lista_episodio .= " " . $texto_para_verificar;
            if ($total_span > $count_span) {
//                        dump($spans[]);
                $dados[$this->remover_caracteres($texto_lista_span[$count_span])][$texto_div][$texto_link] = $link->getAttribute("href");
                $texto_anterior_div = $texto_div;
            }
        }
//
        dump($dados);
//        foreach ($dados as $key => $dado) {
//            dump("Primeira Key: ".$key);
//            foreach ($dado as $key_dado => $d) {
//                dump("Segunda Key: ".$key_dado);
//                foreach ($d as $texto_d2 => $d2) {
//                    dump("Ultima Key: ".$texto_d2);
//                    dump($d2);
//                }
//            }
//        }
//        foreach ($textos as $texto){
//            if(!is_null($texto->nextSibling())){
//                if(count($texto->nextSibling()->find("br")) > 0){
//                    $dados[] = $texto->text();
//                }
//            }
//        }
//        foreach ($spans as $span) {
//            $div = $span->parentNode()->parentNode();
//            for ($i = 0; $i < 2000; $i++) {
//                if (!is_null($div)) {
//                    if(count($div->find("hr")) > 0){
//                        break;
//                    }
//                    $links = $div->find("a[href*='magnet']");
//                    if (count($links) > 0) {
//                        foreach ($links as $link) {
//                            if (count($link->find("img")) > 0) {
//                                $dados[$span->text()]["Temporada Completa"][] = [$this->identificar_qualidade_por_imagem($link->findOne("img")->getAttribute("src")) => $link->getAttribute("href")];
//                            } else {
//                                $dados[$span->text()][$this->remover_texto_links($div->text())][] = [$link->text() => $link->getAttribute("href")];
//                            }
//
//                        }
//                    }
//                    $div = $div->nextSibling();
//                } else {
//                        break;
//                }
//            }
//        }
//        dump($dados);
//        foreach ($dados as $key => $dado) {
//            dump("Primeira Key: ".$key);
//            foreach ($dado as $key_dado => $d) {
//                dump("Segunda Key: ".$key_dado);
//                foreach ($d as $d2) {
//                    foreach ($d2 as $key_d2 => $value) {
//                        dump("Ultima Key: ".$key_d2);
//                        dump($value);
//                    }
//                }
//            }
//        }
    }

    private function remover_caracteres($texto)
    {
        return trim(FuncoesUteis::multipleReplace(["::", "&#8212;", "—"], "", $texto));
    }

    private function identificar_qualidade_por_imagem($link_img)
    {
        if (Str::contains($link_img, "1080")) {
            return "1080p";
        } elseif (Str::contains($link_img, "720")) {
            return "720p";
        } elseif (Str::contains($link_img, "480")) {
            return "480p";
        }
        return false;
    }

    private function remover_texto_links($texto)
    {
        preg_match('/(.*):/', $texto, $resultado);
        return isset($resultado[1]) ? $resultado[1] : $texto;
    }

    private function trocar_imagem_post()
    {
        $endpoint = "https://filmestorrent.vip/xmlrpc.php";

# Create client instance
        $wpClient = new WordpressClient();

# Set the credentials for the next requests
        $wpClient->setCredentials($endpoint, 'jeanderson123', 'AD7MS*c$f5yuFhlBaD&5ZJjr');
        $post_id = 94952;
        dump($wpClient->getPosts(["post_type" => "post"]));
//        dump($wpClient->getPost($post_id));
        $content = $wpClient->getPost($post_id)["post_content"];
        $re = '/<b>Baixar Filme:<\/b>(.*)|<b>Baixar Série:<\/b>(.*)/m';
        $resultado = FuncoesUteis::useRegex($re, $content);

        if (!is_null($resultado)) {
            $nome = empty($resultado[1]) ? $resultado[2] : $resultado[1];
            $tb = new TheMovieDB(TheMovieDB::procurar_filme($nome)["link"]);
            $tb->pegar_dados();
            $img = "img/temp.png";
            FuncoesUteis::baixar_imagem($tb->imagem_capa_link, $img);
            $nome = FuncoesUteis::limpar_caracteres_especiais($nome);
            $nome_pronto = $nome . "filmestorrent-vip.png";
            Imagens::colocar_logo_na_imagem(Sites::FILMESTORRENT_VIP, public_path('img\temp.png'),
                public_path("img\\$nome_pronto"));
            $path_imagem = "../public/img/$nome_pronto";
            $mime = 'image/png';
            $data = file_get_contents($path_imagem);
            $resultado = $wpClient->uploadFile($nome_pronto, $mime, $data, true);
            $url = $resultado["url"];
            $dom = HtmlDomParser::str_get_html($content);
            $dom->findOne("img")->setAttribute("src", $url);
            $content = $dom->html();
            $new_content = ["post_content" => $content, "post_thumbnail" => (int)$resultado["id"]];
            dump($resultado);
            dump($new_content);
            dump($wpClient->editPost($post_id, $new_content));
            dump($wpClient->getPost($post_id));
        } else {
            echo "null";
        }

    }

    private function teste_imdb_imagem()
    {
        $imdb = new IMDB("https://www.imdb.com/title/tt8329148/?ref_=nv_sr_1?ref_=nv_sr_1");
        $imdb->pegar_imagem_capa();
        dump($imdb);
    }

    private function teste_youtube()
    {
        $nome = "Naruto shippuden 1 temporada";
        dump(YouTube::pesquisar_trailer_serie($nome));
    }

    private function teste_comando_filmes()
    {
        $link = "https://comandotorrents.org/wandavision-1a-temporada-torrent";
        $link_the_movie = "85271";
        $link_imdb = "tt9140560";
//        $dom = HtmlDomParser::file_get_html($link);
//        dump($dom->findOne("b:contains('Lançamento')")->nextSibling()->nextSibling()->html());
        //download
//        $links_a = $dom->findMulti("a[href*='magnet']");
//        $links_de_download = [];
//        foreach ($links_a as $link_a){
//            $parent = $link_a->parentNode()->parentNode();
////            if($parent->previousSibling()->previousSibling()->tag == "h2"){
////                dump($parent->previousSibling()->previousSibling()->text());
////            }
//            $links_download = new LinksDownloads();
//            $links_download->texto_links = $parent->text();
//            $links = new LinkFilme();
//            $links->link = $link_a->getAttribute("href");
//            $links->qualidade_link = self::identificar_qualidade_link_texto($parent->text());
//            $links_download->links = $links;
//            $links_de_download[] = $links_download;
//        }
//        dump($links_de_download);
        $html = $this->get_web_page($link);
        $comando = new ComandoTorrent($link);
        $comando->is_serie = true;
        $themovie = new TheMovieDB($link_the_movie, true);
        $imdb = new IMDB($link_imdb);
        $comando->theMovieDB = $themovie;
        $comando->imdb = $imdb;
        $comando->carregar_dados_html($html);
        dump($comando);
//            return "<textarea>".$this->get_web_page($link)."</textarea>";
    }

    private function identificar_qualidade_link_texto($texto)
    {
        if (Str::contains($texto, "720p")) {
            return "720p";
        } else {
            if (Str::contains($texto, "1080p")) {
                return "1080p";
            } else {
                if (Str::contains($texto, "4k")) {
                    return "4k";
                } else {
                    return "";
                }
            }
        }
    }

    private function testeTorrentFilme()
    {
        $endpoint = "http://torrentfilmes.vip/xmlrpc.php";

# Create client instance
        $wpClient = new WordpressClient();

# Set the credentials for the next requests
        $wpClient->setCredentials($endpoint, 'jean', 'k(HvifeFIqwJuRP98HTGyPN^');
        $post_id = 260;
        dump($wpClient->getPost($post_id));
    }

    private function testeSeriesOnline()
    {
        $endpoint = "https://seriesonline.pro/xmlrpc.php";

# Create client instance
        $wpClient = new WordpressClient();

# Set the credentials for the next requests
        $wpClient->setCredentials($endpoint, 'synchronized', 'HN!2)ank$mn%E$GFE3Kd495F');
        $post_id = 884;
        $custom_fields = $wpClient->getPost($post_id);
        dump($custom_fields);
//        $episodios["DUBLADO"] = [1,2,3];
//        $result = $this->regexListaEpisodios($this->procurar_animes_array($custom_fields)["episodios_dublados"]);
//        foreach ($result as $r){
//            $episodios['DUBLADO'][] = $r[1];
//        }
//        dump($episodios);
    }

    private function procurar_animes_array($array)
    {
        $result = [];
        foreach ($array as $a) {
            if ($a["key"] == "episodios_dublados") {
                $result[$a["key"]] = $a["value"];
            } else if ($a["key"] == "episodios_legendados") {
                $result[$a["key"]] = $a["value"];
            }
        }
        return $result;
    }

    private function regexListaEpisodios($episodios_lista)
    {
        $re = '/:"(.*?)";/m';
        preg_match_all($re, $episodios_lista, $matches, PREG_SET_ORDER, 0);
        return $matches;
    }

    private function testeBkSeries()
    {

//        $this->verificar_se_existe_postagem_e_pegar_id();
        set_time_limit(0);
//        $doc = HtmlDomParser::file_get_html("https://www.blogger.com/video.g?token=AD6v5dwBdbtGs3I_VAHlTGYIkqcMWgFNQ99UASzIV_32U4rK1Ankfc7q2nbln9RtqfEU0DaVQWidfrFDV32s443ZUUM1qbHooP9DwBGT2CPlksBZrdAEGN7QSFJ6iOzocRHetUfB6g");
//
//        $script = $doc->findOne("script")->html();
//        $re = '/play_url":"(.*)",/m';
////        $re = 'play_url":"(.*?)/m';
//        preg_match_all($re, $script, $matches, PREG_SET_ORDER, 0);
//        dump($script);
//        dump($matches[0][1]);
//        $this->publicar_Bk_Serie();
//        $link_bk = "https://www.bkseries.com/feud-online/";
//            $link_bk_campanha = "https://www.bkseries.com/campanha.php?t=AD6v5dzN9HTHf_IgIsMXE4d8UmH8mCSe2E7L2zdIIXsJEXrP_ETWYEnca5KFcwvvR6Bx0feEosVAuMaZ2oOk6hmpYijs2B5A9NlsnxC0IjLqAduaoU2wl50BBmwkFn22Zk5lVO-SP5hY";
//            $link_bloger = "https://www.blogger.com/video.g?token=" . explode("t=", $link_bk_campanha)[1];
//            $doc = HtmlDomParser::file_get_html($link_bloger);
//            dump($doc->html());
//            $script = $doc->findOne("script")->html();
//            dump($script);
//            $re = '/&contentid=(.*?)&/m';
//            preg_match_all($re, $script, $matches, PREG_SET_ORDER, 0);
//            $link_id = $matches[0][1];
//            $re = '/play_url":"(.*)",/m';
//            preg_match_all($re, $script, $matches, PREG_SET_ORDER, 0);
//            $link_normal = $matches[0][1];
//            dump($link_id);
//            dump($link_normal);
//        $link_bk_campanha = "https://www.bkseries.com/campanha.php?w=eTQwMHo5amRYUHFPZmc5b1Nkcm9tSlBPbE1kYXgwZ2hzaHFkUWZLWW81N3JoajJzSGFqZGRFOGxDbUJQN2MvOHJ5ZFRUM05vU1M5MnJIM2xudS84eHc9PQ==";
//        $link_preparado = "https://www.bkseries.com/videozin/video-play.mp4/?contentId=" . explode("v=", $link_bk_campanha)[1];
//        $link_wix = "https://www.bkseries.com/video/player/wix.php?w=".explode("w=",$link_bk_campanha)[1];
//        dump($link_wix);
//        dump(HtmlDomParser::file_get_html($link_wix)->findOne("iframe")->getAttribute("src"));
//        $themovie = new TheMovieDB("69851",true);
//        $bk = new BkSeries($link_bk);
//        $bk->theMovieDB = $themovie;
//        $bk->carregar_dados();
//        $bk->pegar_links_para_episodio(false,1,1,3,['DUBLADO']);
//        dump($bk);
//        dump(get_headers($link_preparado));
//        $url= $link_preparado;


// Uncomment to see all headers
        /*
        echo "<pre>";
        print_r($a);echo"<br>";
        echo "</pre>";
        */

//        echo $url;
//        $before = microtime(true);
//        $headers = get_headers($url);
//        dump($headers);
//        $final_url = "";
//        foreach ($headers as $h)
//        {
//            if (substr($h,0,10) == 'Location: ')
//            {
//                $final_url = trim(substr($h,10));
//                break;
//            }
//        }
//        $after = microtime(true);
//        echo ($after-$before) . " sec\n";
//        dump($final_url);
//        $this->get_web_page($link_preparado);
//        $dom = HtmlDomParser::file_get_html("https://www.bkseries.com/bless-this-mess-abencoe-essa-bagunca-online/");
//        $serie_nome = $dom->findOne("span.last-bread")->text();
//        dump($serie_nome);
//        dump($dom->html());
//        $div_tab_content = $dom->findMulti("div.tab_content");
//        $count = count($div_tab_content);
//        dump("temporadas: ".$count);
//        $links_download = [];
//        $temporada = 1;
//        foreach ($div_tab_content as $div_tab){
//            $div_tercos = $div_tab->findMulti("div.um_terco");
//            foreach ($div_tercos as $div_terco){
//                $ul = $div_terco->findOne("div > ul");
//                $tipo = $ul->findOne("p")->text();
//                $li_s = $ul->findMulti("li");
//                foreach ($li_s as $li){
//                    $a = $li->findOne("a");
//                    $texto_link = $a->text();
//                    $link = $a->getAttribute("href");
//                    if(!empty($link)){
//                        $links_download[$temporada][$tipo][] = ["texto"=>$texto_link,"link"=>$link];
//                    }
//                }
//            }
//            $temporada++;
//        }
//        dump($links_download);
        $link_bloger = "https://www.blogger.com/video.g?token=AD6v5dxg_MT-Q2Cv-6HT9keE6hBA5tj1PWCCN4iLDc6SM17pxhn41RNaP1Z4EsRaecvxt-MsHHmgbvyiMWLRF_brSXbccSnt5OhOJ2jEb4kMXRG1YF60nP-LVe1pv3rb9HY-tEFUvvg";
        $doc = HtmlDomParser::file_get_html($link_bloger);
        $script = $doc->findOne("script")->html();
        dump($script);
        dump($doc->html());
    }

    private function publicar_Bk_Serie()
    {
        $link_bk = "https://www.bkseries.com/the-flash-online-5/";
        $themovie = new TheMovieDB("60735", true);
        $bk = new BkSeries($link_bk);
        $bk->theMovieDB = $themovie;
        $config = new ConfigLinksDownload();
        $config->pegar_tudo = false;
        $config->pegar_temporada_e_episodios[1] = [
            "tipos" => ['DUBLADO', 'LEGENDADO'],
            "episodio_start" => 1,
            "episodio_end" => 2
        ];
        $config->pegar_temporada_e_episodios[2] = [
            "tipos" => ['DUBLADO'],
            "episodio_start" => 1,
            "episodio_end" => 1
        ];
        $bk->configLinkDownload = $config;
//        $bk->carregar_dados();
//        $bk->pegar_links_para_episodio();
//        dump($bk);
//        $bk->pegar_links_para_episodio(false,1,1,2,['DUBLADO','LEGENDADO']);
////        dump($bk);
        $lista = [];
        $site = [];
        $lista[] = $bk;
        $site[] = Sites::SERIESONLINE_PRO;
        $wordpress = new WordPress();
        $p = new PublicaSerie();
        dump($p->publicar($wordpress, "teste", $lista, $site));
    }

    private function verificar_se_existe_postagem_e_pegar_id()
    {
        $seriespro = HtmlDomParser::file_get_html("https://seriesonline.pro/?s=" . urlencode("Kimetsu no Yaiba"));
        $div_thumb = $seriespro->findOneOrFalse("div.video-thumb");
        dump($div_thumb);
        if ($div_thumb != false) {
            $a = $div_thumb->findOne("a");
            $link = $a->getAttribute("href");
            $serie = HtmlDomParser::file_get_html($link);
            $postagem = $serie->findOneOrFalse("link[rel='shortlink']");
            if ($postagem != false) {
                $link_com_id = $postagem->getAttribute("href");
                $resultado = str_replace("https://seriesonline.pro/?p=", "", $link_com_id);
                dump($resultado);
            }
        }
    }

    public function get_web_page($url)
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

        $data = explode("\n", $content);
//            dump($content);
//            $result = array_filter($data, function ($var) {
//                return preg_match('/Location: (.*)/m', $var);
//            });
////        dump(trim(str_replace("Location:","",reset($result))));
//            preg_match_all('/&id=(.*?)&/m', reset($result), $matches, PREG_SET_ORDER, 0);
////        dump($matches[0][1]);
//            $after = microtime(true);
//            echo ($after - $before) . " sec\n";
//            \Log::debug($content);
        return $content;
    }

    private function testeDownload()
    {
        $nome = "S.W.A.T. temporada 1";
        $youtube = YouTube::pesquisar_trailer_serie($nome);
        dump($youtube);
    }
}
