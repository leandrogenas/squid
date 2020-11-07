@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Lista Salva","icon"=>"fas fa-folder"])
        <form method="post" action="{{route("json.lista.arrumada")}}">
            @csrf
            <div class="form-group">
                <label>Lista Episódios JSON</label>
                <textarea name="lista" rows="30" class="form-control">{{$texto}}</textarea>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <a href="{{route("home.lista.excluir")}}" class="btn btn-danger">Excluir Arquivo</a>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-success">JUNTAR TUDO</button>
                </div>
            </div>
        </form>

    @endcomponent
@stop
