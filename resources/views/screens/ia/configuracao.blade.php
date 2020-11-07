@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",['title'=>"Configuração da IA"])
        <div class="form-group">
            @include("layouts.componentes.checkbox",['id'=>"ativar_ia","slot"=>"Desativar IA","name"=>"desativar_id"])
        </div>
        <hr>
        <div class="form-group">
            <h3>Filmes</h3>
        </div>
        <div class="form-group">
            <label>Publicar nos Sites</label>
            @php($id = 1)
            @foreach(\App\Enums\Sites::get_movies_sites_active() as $site)
                @component("layouts.componentes.checkbox",["id"=>"checkbox".$id,"value"=>$site,"function"=>"checked","name"=>"sites_filme[]"])
                    {{$site}}
                @endcomponent
                @php($id++)
            @endforeach
        </div>
        <hr>
        <div class="form-group">
            <h3>Series</h3>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <label>Series Torrent Publicar no Sites</label>
                @php($id = 1)
                @foreach(\App\Enums\Sites::get_serie_sites_active() as $site)
                    @component("layouts.componentes.checkbox",["id"=>"checkbox".$id,"value"=>$site,"function"=>"checked","name"=>"sites_serie[]"])
                        {{$site}}
                    @endcomponent
                    @php($id++)
                @endforeach
            </div>
            <div class="col-sm-4">
                <label>Series Online</label>
                @include("layouts.componentes.checkbox",['id'=>"publica_serie_online","slot"=>"Publicar Série Online","name"=>"publica_serie_online","function"=>"checked"])
            </div>
        </div>
        <hr>
        <div class="form-group">
            <h3>Jogos</h3>
        </div>
        <div class="form-group">
            @include("layouts.componentes.checkbox",['id'=>"publica_jogos","slot"=>"Publicar Jogos","name"=>"publica_jogos","function"=>"checked"])
        </div>
        <hr>
        <div class="form-group">
            <h3>Animes</h3>
        </div>
        <div class="form-group">
            @include("layouts.componentes.checkbox",['id'=>"publica_animes","slot"=>"Publicar Animes","name"=>"publica_animes","function"=>"checked"])
        </div>
        <hr>
        <div class="form-group">
            <label>Observações</label>
            <ul>
                <li>Postagens de Filmes que necessitam de IMDB e THEMOVIEDB só serão postadas se for encontrado ambos.
                </li>
                <li>É possível observar as postagens tanto que deram certos quanto nas erradas no menu de Postagens.</li>
                <li>Toda as postagens terão seus logs.</li>
                <li>Na lista de postagens, postagens com mais de 30 dias serão apagadas automáticamente.</li>
            </ul>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Salvar</button>
        </div>
    @endcomponent
@endsection
