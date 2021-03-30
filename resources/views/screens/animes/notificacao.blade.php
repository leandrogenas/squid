@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",['title'=>'Envia Notificação para o app'])
        <form method="post" action="{{route("animes.envia.notificacao")}}">
            @csrf
            <div class="form-group">
                <label>Tópico</label>
                <input class="form-control" name="topic" value="all" required>
            </div>
            <div class="form-group">
                <label>Título</label>
                <input class="form-control" name="title" required>
            </div>
            <div class="form-group">
                <label>Imagem</label>
                <input class="form-control" name="image">
            </div>
            <div class="form-group">
                <label>Corpo da Mensagem</label>
                <textarea class="form-control" name="body" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    @endcomponent
@endsection
