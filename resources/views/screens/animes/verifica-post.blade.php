@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Verifica Post animesvip e orion"])
        <form id="form" method="post">
            @csrf
            <div class="form-group">
                <label>Tipo de Postagem</label>
                @include("layouts.componentes.radio-button",["id"=>"radio1","name"=>"tipo","value"=>"post","text"=>"Anime","checked"=>true])
{{--                @include("layouts.componentes.radio-button",["id"=>"radio2","name"=>"tipo","value"=>"episódio","text"=>"Episódio"])--}}
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <label>A partir do ID</label>
                    <input name="start_id" class="form-control" type="number" value="1">
                </div>
                <div class="col-sm-3">
                    <label>Até o ID</label>
                    <input name="end_id" class="form-control" type="number" value="500000">
                </div>
            </div>
        </form>
        <div class="form-group">
            <label>Resultado</label>
            <textarea id="resultado" rows="5" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <button type="button" onclick="enviar_dados(this)" class="btn btn-success">Verificar</button>
        </div>
    @endcomponent
@endsection
@section("js")
    <script>
        function enviar_dados(button) {
            abrir_loading("Verificando... Aguarde...");
            let btn = $(button);
            btn.prop("disabled", true);
            let data = new FormData(document.getElementById("form"));
            $.ajax({
                type: "post",
                dataType: "json",
                data: data,
                url: "{{route("animes.animesorion.verifica")}}",
                processData: false,
                contentType: false
            }).done(function (data) {
                btn.prop("disabled", false);
                Swal.close();
                Swal.fire(
                    "Pronto",
                    "Pronto man",
                    "success"
                );
                if(data.result){
                    $("#resultado").val(data.result);
                }
            }).fail(function () {
                Swal.close();
                Swal.fire(
                    "Houve um erro",
                    "Verifique o log",
                    "error"
                );
                btn.prop("disabled", false);
            });
        }
    </script>
@endsection
