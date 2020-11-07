@extends('adminlte::page')

@section('content')
    @component("layouts.componentes.box",["title"=>"Publicar Jogos","icon"=> "fas fa-gamepad"])
        <div class="form-group">
            <label>Link do Site</label>
            <input class="form-control" id="link_site">
        </div>
        <div class="form-group">
            <label>Link Magnético</label>
            <input class="form-control" id="link_mag">
        </div>
        <div class="form-group">
            <label>Link NFO</label>
            <input class="form-control" value="#" id="link_nfo">
        </div>
        <div class="form-group">
            @component("layouts.componentes.checkbox",["id"=>"cpt_br","name"=>"pt_br"])
                PT-BR ?
            @endcomponent
            @component("layouts.componentes.checkbox",["id"=>"no_torrent","name"=>"no_torrent"])
                SEM TORRENT ?
            @endcomponent
            @component("layouts.componentes.checkbox",["id"=>"todas_dlc","name"=>"todas_dlc"])
                TODAS DLC ?
            @endcomponent
        </div>
        <hr>
        <div class="form-group">
            <button onclick="adicionar();" class="btn btn-primary" type="button">Adicionar a Lista</button>
        </div>
        <div id="div-status" style="display: none;" class="form-group">
            <div class="container">
                <div class="ring">
                    Aguarde
                    <span></span>
                    <p style="line-height: normal;width: max-content;font-size: 15px;" id="p-status">Progresso</p>
                </div>
            </div>
        </div>
        <div id="lista-publicada" style="display: none;height: 100px;overflow: scroll;" class="form-group">
            <div class="form-group">
                <label>Jogos Publicados</label>
            </div>
            <div class="form-group">
                <ul id="lista-filme"></ul>
            </div>
        </div>
        <div class="form-group">
            <label>Lista de Jogos para publicar</label>
        </div>
        <div class="form-group">
            {{html()->form("post",route("jogos.publicar"))->id("form-publica")->open()}}
            <div class="form-group">
{{--                <label>Publicar nos Sites</label>--}}
                <input type="hidden" value="{{\App\Enums\Sites::JOGOS_TORRENT}}" name="sites[]">
{{--                @php($id = 1)--}}
{{--                @foreach(\App\Enums\Sites::get_games_sites() as $site)--}}
{{--                    @component("layouts.componentes.checkbox",["id"=>$id,"value"=>$site,"function"=>"checked","name"=>"sites[]"])--}}
{{--                        {{$site}}--}}
{{--                    @endcomponent--}}
{{--                    @php($id++)--}}
{{--                @endforeach--}}
            </div>
            <input id="progress" type="hidden" name="progress" value="progress-{{mt_rand(1,30)}}">
            <div class="form-group table-responsive">
                @component("layouts.componentes.table")
                    @slot("table_head")
                        <tr>
                            <th>Link do Site</th>
                            <th>Remover</th>
                        </tr>
                    @endslot
                @endcomponent
            </div>
            {{html()->form()->close()}}
        </div>
        @slot("box_footer")
            <div class="form-group">
                <button id="btn-publica" onclick="publicar()" class="btn btn-success" type="button">Publicar</button>
            </div>
        @endslot
    @endcomponent
@stop
@section("js")
    <script>
        function adicionar() {
            var link = $("#link_site").val();
            var link_mag = $("#link_mag").val();
            var link_nfo = $("#link_nfo").val();
            var pt_br = $("#cpt_br").is(':checked');
            var no_torrent = $("#no_torrent").is(':checked');
            let todas_dlc = $("#todas_dlc").is(':checked');
            var total_tr = $("tbody > tr").length;
            total_tr++;
            $("table > tbody").append(" <tr id='" + total_tr + "'> <td><input type='hidden' name='link_site[]' value='" + link + "'> <input type='hidden' name='links_mag[]' value='" + link_mag + "'> <input type='hidden' name='nfos[]' value='" + link_nfo + "'>" + link + "<input type='hidden' name='is_ptbr[]' value='" + pt_br + "'><input type='hidden' name='no_torrent[]' value='" + no_torrent + "'><input type='hidden' name='todas_dlc[]' value='" + todas_dlc+ "'></td><td><button type='button' onclick=\"remover('tr#" + total_tr + "')\" class='btn btn-danger'><i class='far fa-times-circle'> Remover</i></button></td></tr>");
            limpar_campos();
        }

        function limpar_lista() {
            $("table > tbody > tr").remove();
        }

        function remover(tr_id) {
            $(tr_id).remove();
        }

        function limpar_campos() {
            $("#link_site").val("");
            $("#link_mag").val("");
            $("#link_nfo").val("#");
            $("#cpt_br").prop("checked", false);
            $("#no_torrent").prop("checked", false);
            $("#todas_dlc").prop("checked", false);
        }

        let timer;
        var filme_anterior = "";

        function publicar() {
            if ($("tbody > tr").length <= 0) {
                Swal.fire("Campos Vázio", "É necessário pelo menos um link adicionado", "warning");
            } else {
                $("#div-status").show();
                $("#btn-publica").prop("disabled", true);
                timer = setInterval(function () {
                    pegar_progresso(false);
                }, 1000);
                enviar_dados();
            }
        }

        function pegar_progresso(erro) {
            $.getJSON('{{route("filmes.progresso")}}/?p=' + $("#progress").val(), function (data) {
                $("#p-status").text(data[0]);
                var filme_publicado = data[1];
                var site_publicando = data[2];
                if (filme_publicado !== 0) {
                    $("#lista-publicada").show();
                    if (filme_publicado !== filme_anterior) {
                        $("#lista-filme").append("<li>" + filme_publicado + "</li>");
                        filme_anterior = filme_publicado;
                    }
                }
            });
        }

        function enviar_dados() {
            $.post('{{route("jogos.publicar")}}', $("#form-publica").serialize(), function (msg) {
                var erro = false;
                if (msg.msg === "sucesso") {
                    Swal.fire("Sucesso", "Publicado com sucesso!", "success");
                    limpar_lista();
                } else {
                    Swal.fire("Erro", "Houve um erro! Erro: " + msg.msg, "error");
                    erro = true;
                }
                $("#btn-publica").prop("disabled", false);
                clearInterval(timer);
                $("#div-status").hide();
                setTimeout(function () {
                    pegar_progresso(erro);
                }, 3000);
            }, 'json').fail(function () {
                Swal.fire("Erro", "Houve um erro!", "error");
                clearInterval(timer);
                $("#div-status").hide();
                $("#btn-publica").prop("disabled", false);
            });
        }
    </script>
@stop
