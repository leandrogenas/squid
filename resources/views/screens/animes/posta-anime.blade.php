@extends('adminlte::page')
@section('content')
    @component("layouts.componentes.box",["title"=>"Fazer Postagem de Anime"])
        <div class="form-group">
            <label>Informe a URL do anime (qualquer página referente ao anime)</label>
            <input id="url" class="form-control">
        </div>
        <div id="content" class="form-group">
            <input name="url[]" type="hidden">
            <input name="imagem_themovie[]" type="hidden">
            <div class="form-group">
                <label>Nome do Anime</label>
                <input name="anime[]" class="form-control">
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <label>ID animesonlinevip</label>
                    <input type="number" name="vip_id[]" class="form-control">
                    <small>Se não tiver a postagem deixe em branco.</small>
                </div>
                <div class="col-sm-4">
                    <label>Selecione a Postagem</label>
                    <select name="select_postagem" class="form-control"></select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <label>ID themovie</label>
                    <input type="number" name="id_themovie[]" class="form-control">
                </div>
                <div class="col-sm-4">
                    <label>Selecione o themovie</label>
                    <select name="select_themovie" class="form-control"></select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <label>Tipo de Postagem</label>
                    <select class="form-control" name="tipo[]">
                        <option notclear="1" value="{{\App\Enums\AnimeTipo::EPISODIO}}">Episódio</option>
                        <option notclear="1" value="{{\App\Enums\AnimeTipo::FILME}}">Filme</option>
                        <option notclear="1" value="{{\App\Enums\AnimeTipo::OVA}}">OVA</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1">
                    <label>Ano:</label>
                    <input name="ano[]" class="form-control" value="{{\Carbon\Carbon::now()->year}}">
                </div>
                <div class="col-sm-1">
                    <label>Da Posição</label>
                    <input type="number" name="ep_start[]" min="1" class="form-control" value="1">
                </div>
                <div class="col-sm-1">
                    <label>Até a Posição:</label>
                    <input type="number" name="ep_end[]" min="1" class="form-control" value="2">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Capa Selecionada</label>
            <div id="capa_selecionada">

            </div>
            <small>Se tiver vázia será utilizado o do animesvision</small>
        </div>
        <div class="form-group">
            @component("layouts.componentes.painel-collapse",["title"=>"Selecione a Capa"])
                <div id="content_themovie">
                </div>
            @endcomponent
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
    <script src="{{asset("js/jquery.fix.clone.js")}}"></script>
    <script>
        let url_anterior = "";
        let div_content = $("#content");
        $("#url").keyup(function () {
            let url = this.value;
            if (url !== url_anterior) {
                if (url) {
                    procuraranime(url);
                }
                url_anterior = url;
                let numero = regexurl(url);
                if (numero) {
                    let n = parseInt(numero);
                    div_content.find("input[name='ep_start[]']").val(n);
                    div_content.find("input[name='ep_end[]']").val(n);
                }
            }
        });

        function regexurl(url) {
            const regex = /episodio-(.*?)\//gm;
            let m;
            let resultado = false;
            while ((m = regex.exec(url)) !== null) {
                if (m.index === regex.lastIndex) {
                    regex.lastIndex++;
                }
                resultado = m[1];
                break;
            }
            return resultado;
        }

        function limpar_lista() {
            $("table > tbody").find("tr").remove();
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
                url: "{{route("anime.publicar")}}",
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

        function add() {
            let table = $("#table");
            let nome_anime = $("#url").val();
            let count_tr = table.find("tbody > tr").length;
            table.find("tbody").append("<tr id='tr_" + count_tr + "'><td>" + nome_anime + "</td><td><button onclick=\"remover('#tr_" + count_tr + "')\" class='btn btn-danger'><i class='fas fa-times'></i></button><div id='content_" + count_tr + "' style='display: none'></div></td></tr>");
            let c = $("#content").children().clone();
            c.appendTo("#content_" + count_tr);
            limpar_campos();
        }

        function procuraranime(url) {
            limpar_campos(false);
            abrir_loading("Buscando aguarda...");
            $.ajax({
                type: "post",
                dataType: "json",
                url: "{{route("anime.procura.vip")}}",
                data: {_token: "{{csrf_token()}}", url: url}
            }).done(function (data) {
                Swal.close();
                console.log(data);
                if (!data.error) {
                    div_content.find("input[name='url[]']").val(data.url);
                    let selectOrion = div_content.find("select[name='select_postagem']");
                    if (data.lista) {
                        data.lista.forEach(function (item, index) {
                            if (index === 0) {
                                div_content.find("input[name='vip_id[]']").val(item.id);
                            }
                            selectOrion.append("<option value='" + item.id + "'>" + item.id + " - " + item.link + "</option>");
                        });
                    }
                    if (data.lista_themovie) {
                        let selectthemovie = div_content.find("select[name='select_themovie']");
                        data.lista_themovie.forEach(function (item, index) {
                            if (index === 0) {
                                div_content.find("input[name='id_themovie[]']").val(item.id);
                                procurar_imagens(item.id,item.media_type);
                            }
                            selectthemovie.append("<option media-type='"+item.media_type+"' value='" + item.id + "'>" + item.id + " - " + item.name + "</option>");
                        });
                    }
                    if(data.count_episodios){
                        div_content.find("input[name='ep_end[]']").val(data.count_episodios);
                    }
                    if(data.titulo_anime){
                        div_content.find("input[name='anime[]']").val(data.titulo_anime);
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

        function procurar_imagens(id,type = "tv") {
            $.ajax({
                type: "post",
                dataType: "json",
                url: "{{route("anime.themovie.imagens")}}",
                data: {_token: "{{csrf_token()}}", id: id,type:type}
            }).done(function (data) {
                console.log(data);
                if(data.lista){
                    let div_themovie = $("#content_themovie");
                    div_themovie.html("");
                    data.lista.forEach(function (item,index) {
                        if(index === 0){
                            div_content.find("input[name='imagem_themovie[]']").val(item.imagem);
                            $("#capa_selecionada").html("");
                            $("#capa_selecionada").append("<img style='width: 120px;' src='"+item.imagem+"' class='img-thumbnail'>");
                        }
                        div_themovie.append("<img style='width: 120px;cursor: pointer' onclick='selecionaimagem(this);' src='"+item.imagem+"' class='img-thumbnail'>");
                    })
                }
            }).fail(function () {
                Swal.fire(
                    "Houve um erro ao pegar imagens themovie",
                    "Verifique o log",
                    "error"
                );
            });
        }

        function selecionaimagem(imagem) {
            let img = $(imagem);
            let capa = $("#capa_selecionada");
            capa.html("");
            capa.append("<img style='width: 120px;' src='"+img.attr("src")+"' class='img-thumbnail'>");
            div_content.find("input[name='imagem_themovie[]']").val(img.attr("src"));
        }

        function remover(tr) {
            $(tr).remove();
        }

        function limpar_campos(limpar_url = true) {
            if(limpar_url){
                $("#url").val("");
            }
            $("#capa_selecionada").html("");
            $("#content_themovie").html("");
            div_content.find("#radio1").prop("checked", true);
            div_content.find("input:not([name='tipo[]'])").val("");
            div_content.find("option:not([notclear*='1'])").remove();
            div_content.find("input[name='ep_start[]']").val("1");
            div_content.find("input[name='ep_end[]']").val("2");
            div_content.find("input[name='ano[]']").val("{{\Carbon\Carbon::now()->year}}");
        }

        div_content.find("select[name='select_postagem']").on('change', function () {
            div_content.find("input[name='vip_id[]']").val(this.value);
        });
        div_content.find("select[name='select_themovie']").on('change', function () {
            div_content.find("input[name='id_themovie[]']").val(this.value);
            let type = div_content.find("select[name='select_themovie'] > option:selected").attr("media-type");
            procurar_imagens(this.value,type);
        });
        div_content.find("input[name='id_themovie[]']").keyup(function () {
            if (this.value) {
                procurar_imagens(this.value);
            }
        });
    </script>
@endsection
