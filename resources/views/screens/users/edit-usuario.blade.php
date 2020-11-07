@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Cadastrar usuário","icon"=> "fas fa-users"])
        <form method="post" action="{{route("usuarios.update",["id"=>$usuario->id])}}">
            @csrf
            <div class="form-group">
                <label>Nome: </label>
                <input required class="form-control" name="name" value="{{$usuario->name}}">
            </div>
            <div class="form-group">
                <label>Email: </label>
                <input type="email" required class="form-control" name="email" value="{{$usuario->email}}">
            </div>
            <div class="form-group">
                <label>Nova Senha: </label>
                <input type="password" class="form-control" name="password">
                <small>Deixe em branco e a senha vai permanecer.</small>
            </div>
            <div class="form-group">
                <label>Permissões</label>
                @foreach(\App\Enums\PermissoesTipo::getValues() as $permissao)
                    <div class="form-group">
                        <input type="checkbox" {!! $usuario->hasPermissionTo($permissao) ? "checked":"" !!} name="permissoes[]" value="{{$permissao}}"> {{$permissao}}
                    </div>
                @endforeach
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">SALVAR ALTERAÇÕES</button>
            </div>
        </form>
    @endcomponent
@endsection
