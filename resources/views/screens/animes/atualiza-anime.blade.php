@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Atualizar Postagem"])
        <div id="div_content_data">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Informe a URL do {{\App\Enums\Sites::ANIMESONLINE_VIP}}</label>
                        <input name="animesonlinevip[]" class="form-control">
                        <input type="hidden"  name="animesonlinevip_id[]">
                    </div>
                    <div class="form-group">
                        <label>Postagem encontrada</label>
                        <input name="postagem_encontrada" class="form-control" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Selecione o Anime do animesorion</label>
                        <select class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label>Forneça o ID do animesorion</label>
                        <input type="number" name="animesorion[]" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Nome do Anime</label>
                <input name="anime[]" class="form-control">
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <label>Pegar do Episódio</label>
                        <input class="form-control" name="ep_start[]" type="number" min="1" value="1">
                    </div>
                    <div class="col-md-2">
                        <label>Até</label>
                        <input class="form-control" name="ep_end[]" type="number" min="1" value="2">
                    </div>
                    <div class="col-md-2">
                        <button style="margin-top: 32px;" onclick="add()" type="button" class="btn btn-primary">
                            Adicionar na Lista
                        </button>
                    </div>
                </div>
            </div>
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
            <div class="form-group">
                <button type="button" onclick="enviar_dados(this)" class="btn btn-success">Fazer Atualização</button>
            </div>
        </form>
        <div class="form-group">
            <label>Log result</label>
            <textarea id="result_content" class="form-control"></textarea>
        </div>
    @endcomponent
@endsection
@section("js")
    <script src="{{asset("js/jquery.fix.clone.js")}}"></script>
    <script>
        let dado_anterior = "";
        $("#div_content_data").find("input[name='animesonlinevip[]']").keyup(function () {
            let dado = $(this).val();
            if(dado !== dado_anterior){
                if (dado) {
                    procurar_dados(dado);
                    dado_anterior = dado;
                }
            }
        });

        function limpar_campos() {
            let div_content = $("#div_content_data");
            div_content.find("input").val("");
            div_content.find("option").remove();
            div_content.find("input[name='ep_start[]']").val("1");
            div_content.find("input[name='ep_end[]']").val("2");
        }

        function add() {
            let table = $("#table");
            let nome_anime = $("#div_content_data").find("input[name='anime[]']").val();
            let count_tr = table.find("tbody > tr").length;
            table.find("tbody").append("<tr id='tr_" + count_tr + "'><td>" + nome_anime + "</td><td><button onclick=\"remover('#tr_" + count_tr + "')\" class='btn btn-danger'><i class='fas fa-times'></i></button><div id='content_" + count_tr + "' style='display: none'></div></td></tr>");
            let c = $("#div_content_data").children().clone();
            c.appendTo("#content_" + count_tr);
            limpar_campos();
        }

        function remover(tr) {
            $(tr).remove();
        }

        function enviar_dados(button) {
            abrir_loading("Fazendo atualização... Aguarde...");
            let btn = $(button);
            btn.prop("disabled", true);
            let data = new FormData(document.getElementById("form"));
            $.ajax({
                type: "post",
                dataType: "json",
                data: data,
                url: "{{route("anime.atualizar")}}",
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
        let div_content = $("#div_content_data");
        $('select').on('change', function() {
            div_content.find("input[name='animesorion[]']").val(this.value);
        });

        function limpar_lista() {
            $("table > tbody").find("tr").remove();
        }

        function procurar_dados(url_animesvip) {
            abrir_loading("Buscando aguarda...");
            $.ajax({
                type: "post",
                dataType: "json",
                url: "{{route("anime.procura")}}",
                data: {_token: "{{csrf_token()}}", url_animesvip: url_animesvip}
            }).done(function (data) {
                Swal.close();
                let div_content = $("#div_content_data");
                let selectOrion =  div_content.find("select");
                if(!data.erro){
                    if(data.lista){
                        data.lista.forEach(function (item,index) {
                            if(index === 0){
                                div_content.find("input[name='animesorion[]']").val(item.id);
                            }
                            selectOrion.append("<option value='"+item.id+"'>"+item.id+" - "+item.title+"</option>");
                        });
                    }
                    div_content.find("input[name='postagem_encontrada'").val(data.id_vip+" - "+data.post_name_vip);
                    div_content.find("input[name='animesonlinevip_id[]']").val(data.id_vip);
                    div_content.find("input[name='anime[]']").val(data.title_vip);
                    div_content.find("input[name='ep_end[]']").val(data.episodio_total);
                }else{
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
    </script>
@endsection
