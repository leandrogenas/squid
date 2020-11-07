@extends('adminlte::page')
@section("content")
    @component("layouts.componentes.box",["title"=>"Procurar Imagens Offline"])
        <form method="post" id="form">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <label>Da Pagina</label>
                    <input required type="number" name="page_start" value="2" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Até Pagina</label>
                    <input  required type="number" name="page_end" value="871" class="form-control">
                </div>
                <div class="col-md-3">
                    <button style="margin-top: 31px;" onclick="procurar()" type="button" class="btn btn-success">Procurar</button>
                </div>
            </div>
        </form>
        <div class="form-group">
            <label>IDs encontrados</label>
            <textarea id="resultados" class="form-control" rows="4"></textarea>
        </div>
    @endcomponent
@endsection
@section("js")
    <script>
        function procurar() {
            abrir_loading("Procurando... Aguarde....");
            $.post('{{route("filmes.procura.imagem.action")}}', $("#form").serialize(), function (data) {
                $("#resultados").val(data.result);
                Swal.close();
            }, 'json').fail(function () {
                Swal.close();
                Swal.fire("Erro", "Houve um erro!", "error");
            });
        }
    </script>
@endsection
