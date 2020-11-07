@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Pegar Dados de Curso"])
        <form method="post" id="form">
            @csrf
            <div class="form-group">
                <label>Cookies</label>
                <div class="row row-cols-2">
                    <div class="col">
                        <label>Cookie Name:</label>
                        <input class="form-control" name="cookie_name"
                               value="wordpress_logged_in_cd6291735c0896f7018ae40498adfd54">
                    </div>
                    <div class="col">
                        <label>Cookie Value:</label>
                        <input class="form-control" name="cookie_value"
                               value="Willian+Montanaro+Esteves+Araujo%7C1594302531%7C6Srh1nPgIPVAc64WYM5RVwI3aaM601E1zUAvnkEd92y%7Cff9d05fdce6f028c0a96578ecb9541ee16a7c9591344674d644afe668ef69eae">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Link da Seção</label>
                <input class="form-control" name="link" required>
                <small>Ex: https://vip.seodeverdade.pro/courses/seo-on-page/7929-729</small>
            </div>
        </form>
        <div class="form-group">
            <label>Resultado JSON</label>
            <textarea class="form-control" rows="4" id="resultado"></textarea>
        </div>
        <div class="form-group">
            <label>Log</label>
            <textarea class="form-control" id="log"></textarea>
        </div>
        <div class="form-group">
            <button onclick="enviar_dados(this)" type="button" class="btn btn-success">Iniciar</button>
        </div>
    @endcomponent
@endsection
@section("js")
    <script>
        function enviar_dados(button) {
            abrir_loading("Publicando... Aguarde...");
            let btn = $(button);
            btn.prop("disabled", true);
            let data = new FormData(document.getElementById("form"));
            $.ajax({
                type: "post",
                dataType: "json",
                data: data,
                url: "{{route("cursos.start")}}",
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
                $("#resultado").val(data.resultado);
                $("#log").val(data.log);
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
