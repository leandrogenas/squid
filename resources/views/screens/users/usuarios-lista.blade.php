@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Usuários Cadastrados","icon"=> "fas fa-users"])
        <div class="form-group">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Opções</th>
                </tr>
                </thead>
                <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{$usuario->name}}</td>
                        <td><a href="{{route("usuarios.edit",["id"=>$usuario->id])}}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endcomponent
@endsection
