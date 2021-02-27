<?php

    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    Route::get('/', function () {
        return redirect()->route("home");
    });

    Auth::routes(['register' => false]);

    Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');
//google drive

    Route::get('login/google/callback', 'GoogleDriveController@handleProviderGoogleCallback');
    Route::get('login/evernote/callback', 'GoogleDriveController@handleProviderGoogleCallback');

//Route::get("/testes/lapumia","TestController@lapumiaDados");
    Route::any("/testes", "TestController@testes")->middleware("auth")->name('testes');
    Route::group(["middleware" => "auth", "prefix" => "home"], function () {
        Route::post("/filmes/series/pesquisa", "FilmesController@pesquisar_links_serie")->name("filmes.pesquisa.serie");
        Route::post("/filmes/pesquisa", "FilmesController@pesquisar_links")->name("filmes.pesquisa");
        Route::get("/filmes/progresso/", "FilmesController@get_progresso")->name("filmes.progresso");
        Route::get("/filmes/trocaimagem", "FilmesController@tela_troca_imagem")->name("filmes.trocaimagem");
        Route::post("/filmes/trocaimagem/post", "FilmesController@trocar_imagem")->name("filmes.trocaimagem.post");
        Route::get("/series", "FilmesController@create_serie")->name("series.create");
        Route::get("/series/update", "SerieController@atualizar_serie_tela")->name("serie.tela.update");
        Route::post("/series/pesquisa", "SerieController@pesquisar_links_serie_atualiza")->name("serie.pesquisa.update");
        Route::post("/series/update/start", "SerieController@atualizar_serie")->name("serie.update");
        Route::get("/jogos", "JogoController@index")->name("jogos.index");
        Route::post("/jogos/publicar", "JogoController@publicar")->name("jogos.publicar");
        Route::get("/limpar", "HomeController@limpar_cache")->name("limpar.cache");
        Route::post("/serie/somente/pesquisa", "SerieController@pesquisa_links_somente_serie")->name("serie.somente.pesquisa");
        Route::get("/season", "SerieNovoController@index")->name("season.index");
        Route::post("/season/postar", "SerieNovoController@postar")->name("season.postar");
        Route::post("/season/pesquisar", "SerieNovoController@pesquisar")->name("season.pesquisar");
        Route::get("/season/progress", "SerieNovoController@get_progresso")->name("season.progresso");
        Route::get("/json/lista", "HomeController@lista_episodios")->name("home.lista.episodios");
        Route::get("/json/lista/remove", "HomeController@excluir_lista")->name("home.lista.excluir");
        Route::post("/json/search", "SerieNovoController@getJsonResult")->name("serie.json.result");
        Route::get("/filmes/procuraimagem", "FilmesController@procura_imagem")->name("filme.procura.imagem");
        Route::post("/filmes/procuraimagem/action", "FilmesController@action_procurar_imagem")->name("filmes.procura.imagem.action");
        Route::get("/animes/atualizar", "AnimesController@pagina_atualiza_anime")->name("anime.pagina.atualiza");
        Route::post("/animes/procurar", "AnimesController@procurar_animesorion")->name("anime.procura");
        Route::post("/animes/atualizar", "AnimesController@atualizar_animes")->name("anime.atualizar");
        Route::get("/animes/postagem", "AnimesController@telaPosta")->name("anime.tela.posta");
        Route::post("/animes/procura/vip", "AnimesController@procurarAnimePost")->name("anime.procura.vip");
        Route::post("/animes/publicar", "AnimesController@publicar")->name("anime.publicar");
        Route::post("/animes/themovie/imagens", "AnimesController@imagemthemovie")->name("anime.themovie.imagens");
        Route::get("/animes/animesorion/postagem", "AnimesController@telaPostagemOrion")->name("animes.postagem.orion");
        Route::post("/animes/animesorion/procura", "AnimesController@procuraAnimeVipEOrion")->name("animes.animesorion.procura");
        Route::post("/animes/animesorion/publica", "AnimesController@publicarOrion")->name("animes.animesorion.publica");
        Route::get("/animes/animesorion/verifica", "AnimesController@telaVerificarPostagemOrion")->name("animes.animesrion.telaverifica");
        Route::post("/animes/animesorion/verificapostagem", "AnimesController@verificarPostagemOrionEVip")->name("animes.animesorion.verifica");
        Route::get("/ia/config", "AutoPostController@configuracao")->name("ia.config");
        Route::get("/curso", "TestController@pagina_cursos")->name("cursos");
        Route::post("/curso/start", "TestController@carregar_dados_cursos")->name("cursos.start");
        Route::get("/feed/ultimoslinks","Feed\FeedController@ultimos_links")->name("feed.ultimoslinks");

        //usuarios
        Route::get("/usuarios","UsuariosController@index")->name("usuarios.lista");
        Route::get("/usuarios/create","UsuariosController@create")->name("usuarios.create");
        Route::post("/usuarios/cadastrar","UsuariosController@cadastrar")->name("usuarios.cadastrar");
        Route::get("/usuarios/edit/{id}","UsuariosController@edit")->name("usuarios.edit");
        Route::post("/usuarios/update/{id}","UsuariosController@update")->name("usuarios.update");

        Route::post("/json/lista/arrumadinha","SerieNovoController@juntaListaJson")->name("json.lista.arrumada");

        Route::resource("filmes", "FilmesController");
    });
    //twitter
    Route::get('twitter/login', ['as' => 'twitter.login', function () {
        // your SIGN IN WITH TWITTER  button should point to this route
        $sign_in_twitter = true;
        $force_login = false;

        // Make sure we make this request w/o tokens, overwrite the default values in case of login.
        Twitter::reconfig(['token' => '', 'secret' => '']);
        $token = Twitter::getRequestToken(route('twitter.callback'));
        Log::debug(print_r($token,true));
        if (isset($token['oauth_token_secret'])) {
            $url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);

            Session::put('oauth_state', 'start');
            Session::put('oauth_request_token', $token['oauth_token']);
            Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

            return Redirect::to($url);
        }

        return Redirect::route('twitter.error');
    }]);

    Route::get('twitter/callback', ['as' => 'twitter.callback', function () {
        // You should set this route on your Twitter Application settings as the callback
        // https://apps.twitter.com/app/YOUR-APP-ID/settings
        if (Session::has('oauth_request_token')) {
            $request_token = [
                'token' => Session::get('oauth_request_token'),
                'secret' => Session::get('oauth_request_token_secret'),
            ];
            Log::debug(print_r($request_token,true));
            Twitter::reconfig($request_token);

            $oauth_verifier = false;

            if (\Illuminate\Support\Facades\Request::has('oauth_verifier')) {
                $oauth_verifier = \Illuminate\Support\Facades\Request::get('oauth_verifier');
                // getAccessToken() will reset the token for you
                $token = Twitter::getAccessToken($oauth_verifier);
                Log::debug(print_r($token,true));
            }

            if (!isset($token['oauth_token_secret'])) {
                return Redirect::route('twitter.error')->with('flash_error', 'We could not log you in on Twitter.');
            }

            $credentials = Twitter::getCredentials();

            if (is_object($credentials) && !isset($credentials->error)) {
                // $credentials contains the Twitter user object with all the info about the user.
                // Add here your own user logic, store profiles, create new users on your tables...you name it!
                // Typically you'll want to store at least, user id, name and access tokens
                // if you want to be able to call the API on behalf of your users.

                // This is also the moment to log in your users if you're using Laravel's Auth class
                // Auth::login($user) should do the trick.

                Session::put('access_token', $token);

                return Redirect::to('/')->with('flash_notice', 'Congrats! You\'ve successfully signed in!');
            }

            return Redirect::route('twitter.error')->with('flash_error', 'Crab! Something went wrong while signing you up!');
        }
    }]);

    Route::get('twitter/error', ['as' => 'twitter.error', function () {
        // Something went wrong, add your own error handling here
    }]);

    Route::get('twitter/logout', ['as' => 'twitter.logout', function () {
        Session::forget('access_token');
        return Redirect::to('/')->with('flash_notice', 'You\'ve successfully logged out!');
    }]);

    Route::get("narro/callback",function (){
        dump(Request::all());
        $code = Request::get("code");
        $cliente = new \GuzzleHttp\Client();
        $response = $cliente->request('POST', 'https://www.narro.co/oauth2/token', [
            'form_params' => [
                'client_id' => 'c1552200-7ec7-4da4-941d-e5e6466a5cbc',
                'client_secret' => 'bc8110a3-37c0-478b-8c38-6b96dedd72dac1f7e7bc-666c-40be-8c19-9121dbb5bf76',
                'grant_type'=>'authorization_code',
                'redirect_uri'=>'http://localhost/SyncWeb/public/narro/callback',
                'code'=>$code
            ]
        ]);
        dump(json_decode($response->getBody()->getContents()));
    });
