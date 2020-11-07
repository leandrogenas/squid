@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Cadastrar usuário","icon"=> "fas fa-users"])
        <form method="post" action="{{route("usuarios.cadastrar")}}">
            @csrf
            <div class="form-group">
                <label>Nome: </label>
                <input required class="form-control" name="name">
            </div>
            <div class="form-group">
                <label>Email: </label>
                <input type="email" required class="form-control" name="email">
            </div>
            <div class="form-group">
                <label>Senha: </label>
                <input type="password" required class="form-control" name="password">
            </div>
            <div class="form-group">
                <label>Permissões</label>
                @foreach(\App\Enums\PermissoesTipo::getValues() as $permissao)
                    <div class="form-group">
                        <input type="checkbox" name="permissoes[]" value="{{$permissao}}"> {{$permissao}}
                    </div>
                @endforeach
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">SALVAR</button>
            </div>
        </form>
    @endcomponent
@endsection
