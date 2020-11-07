@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Fazer Postagem com AnimesOrion"])
        <div class="form-group">
            <label>Link do AnimesOrion (Somente o URL do anime)</label>
            <input class="form-control" id="url">
        </div>
        <div class="form-group">
            <label>Encontrado na pesquisa orion</label>
            <input readonly type="text" class="form-control" id="resultado_orion">
        </div>
        <div class="form-group" id="content">
            <div class="form-group">
                <label>Nome do Anime</label>
                <input name="anime[]" class="form-control">
            </div>
            <div class="row">
                <div class="col-sm-1">
                    <label>ID animesorion</label>
                    <input type="number" class="form-control" name="id_orion[]">
                </div>
                <div class="col-sm-2">
                    <label>ID animesonlinevip</label>
                    <input type="number" class="form-control" name="vip_id[]">
                </div>
                <div class="col-sm-4">
                    <label>Selecione a postagem em animesonlinevip</label>
                    <select name="select_postagem" class="form-control"></select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1">
                    <label>Do Episódio:</label>
                    <input type="number" name="ep_start[]" min="1" class="form-control" value="1">
                </div>
                <div class="col-sm-1">
                    <label>Até o Episódio:</label>
                    <input type="number" name="ep_end[]" min="1" class="form-control" value="2">
                </div>
            </div>
        </div>
        <div class="form-group">
            <button onclick="add();" type="button" style="margin-top: 15px;" class="btn btn-primary">Adicionar a Lista
            </button>
        </div>
        <form id="form" method="post">
            @csrf
            <div class="form-group">
                <label>Adicionados para postagem</label>
                <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Anime</th>
                        <th>Remover</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </form>
        <div class="form-group">
            <button type="button" onclick="enviar_dados(this)" class="btn btn-success">Publicar</button>
        </div>
        <div class="form-group">
            <label>Log result</label>
            <textarea id="result_content" class="form-control"></textarea>
        </div>
    @endcomponent
@endsection
@section("js")
    <script>
        let url_anterior = "";
        let div_content = $("#content");
        $("#url").keyup(function () {
            let url = this.value;
            if (url !== url_anterior) {
                procuraranime(url);
                url_anterior = url;
            }
        });
        function limpar_lista() {
            $("table > tbody").find("tr").remove();
        }
        function remover(tr) {
            $(tr).remove();
        }
        function add() {
            let table = $("#table");
            let nome_anime = $("#url").val();
            let count_tr = table.find("tbody > tr").length;
            table.find("tbody").append("<tr id='tr_" + count_tr + "'><td>" + nome_anime + "</td><td><button onclick=\"remover('#tr_" + count_tr + "')\" class='btn btn-danger'><i class='fas fa-times'></i></button><div id='content_" + count_tr + "' style='display: none'></div></td></tr>");
            let c = $("#content").children().clone();
            c.appendTo("#content_" + count_tr);
            limpar_campos();
        }
        function limpar_campos(limpar_url = true) {
            if(limpar_url){
                $("#url").val("");
            }
            div_content.find("input").val("");
            div_content.find("option").remove();
            div_content.find("input[name='ep_start[]']").val("1");
            div_content.find("input[name='ep_end[]']").val("2");
        }
        div_content.find("select[name='select_postagem']").on('change', function () {
            div_content.find("input[name='vip_id[]']").val(this.value);
        });
        function procuraranime(url) {
            limpar_campos(false);
            abrir_loading("Buscando aguarda...");
            $.ajax({
                type: "post",
                dataType: "json",
                url: "{{route("animes.animesorion.procura")}}",
                data: {_token: "{{csrf_token()}}", url: url}
            }).done(function (data) {
                Swal.close();
                console.log(data);
                if (!data.error) {
                   div_content.find("input[name='id_orion[]']").val(data.id_orion);
                   $("#resultado_orion").val(data.title_orion);
                    div_content.find("input[name='anime[]']").val(data.title_orion);
                    div_content.find("input[name='ep_end[]']").val(data.count_ep);
                    if (data.lista) {
                        let selectOrion = div_content.find("select[name='select_postagem']");
                        data.lista.forEach(function (item, index) {
                            if (index === 0) {
                                div_content.find("input[name='vip_id[]']").val(item.id);
                            }
                            selectOrion.append("<option value='" + item.id + "'>" + item.id + " - " + item.link + "</option>");
                        });
                    }
                } else {
                    Swal.fire(
                        "Houve um erro",
                        data.msg,
                        "error"
                    );
                }
            }).fail(function () {
                Swal.close();
                Swal.fire(
                    "Houve um erro",
                    "Verifique o log",
                    "error"
                );
            })
        }
        function enviar_dados(button) {
            abrir_loading("Publicando... Aguarde...");
            let btn = $(button);
            btn.prop("disabled", true);
            let data = new FormData(document.getElementById("form"));
            $.ajax({
                type: "post",
                dataType: "json",
                data: data,
                url: "{{route("animes.animesorion.publica")}}",
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
                $("#result_content").val(data.log);
                limpar_lista();
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
