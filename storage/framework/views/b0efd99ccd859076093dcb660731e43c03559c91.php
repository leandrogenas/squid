<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent("layouts.componentes.box",["title"=>"Publicar Séries (NOVO)","icon"=> "fas fa-video"]); ?>
        <div id="div-status" style="display: none;" class="form-group">
            <div class="container">
                <div class="ring">
                    Aguarde
                    <span></span>
                    <p style="line-height: normal;width: max-content;font-size: 15px;" id="p-status">Progresso</p>
                </div>
            </div>
        </div>
        <div class="form-group">
            <a id="themovie_pesquisa_manual" class="btn btn-primary" target="_blank"
               href="https://www.themoviedb.org/search?query=&language=pt-BR">Pesquisa TheMovieDB manual</a>
        </div>
        <div id="content_dados">
            <div id="dados">
                <div>
                    <div class="form-group">
                        <div class="form-group">
                            <label>Link Suportado</label>
                            <input name="link[]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Nome da Série</label>
                            <input name="serie_name[]" class="form-control">
                        </div>
                        <div class="form-group">
                            <p>Cole o link aqui para pegar o id</p>
                            <label>Link ThemovieDB</label>
                            <input class="form-control" name="link_themovie[]">
                        </div>
                        <div class="form-group">
                            <label>ID ThemovieDB</label>
                            <input name="id_themovie[]" readonly class="form-control">
                            <input name="nome_themovie[]" placeholder="Nome encontrado themoviedb" class="form-control" readonly>
                        </div>
                        <hr>
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>Publicar Tudo?</label>
                                    <select name="todas_temporadas[]" class="form-control">
                                        <option value="1">SIM</option>
                                        <option value="0">NÃO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php $__env->startComponent("layouts.componentes.painel-collapse",["title"=>"Opções"]); ?>
                                <div class="form-group" name="content_temporada_vista">

                                </div>
                                <div class="form-group">
                                    <button type="button" onclick="add_temporada()" title="Adicionar mais" class="btn btn-primary">
                                        <i class="fas fa-plus"></i></button>
                                    <button style="margin-left: 15px;" type="button" onclick="remove_temporada()"
                                            title="remover ultimo campo" class="btn btn-danger"><i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <button type="button" onclick="add()" class="btn btn-success">Adicionar</button>
        </div>
        <hr>
        <form id="form-publica" method="post" action="<?php echo e(route("season.postar")); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Adicionados</label>
                <div class="form-group">
                    <?php $__env->startComponent("layouts.componentes.table"); ?>
                        <?php $__env->slot("table_head"); ?>
                            <tr>
                                <th>Link</th>
                                <th>Postado</th>
                                <th>Opção</th>
                            </tr>
                        <?php $__env->endSlot(); ?>
                    <?php echo $__env->renderComponent(); ?>
                </div>
            </div>
        </form>
        <div class="form-group">
            <button type="button" id="btn-publica" onclick="enviar_dados()" class="btn btn-success">POSTAR</button>
            <button class="btn btn-danger" type="button" onclick="limpar_lista()">Limpar Adicionados</button>
        </div>
        <div class="form-group">
            <label>JSON Result</label>
            <textarea id="json_textarea" rows="5" class="form-control">
            </textarea>
        </div>
    <?php echo $__env->renderComponent(); ?>
    <div hidden>
        <div id="content_temporada">
            <div class="row">
                <div class="col-sm-2">
                    <label>Temporada</label>
                    <input type="number" name="temporada" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label>Do episódio</label>
                    <input type="number" name="episodio_start" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label>Até Episódio</label>
                    <input type="number" name="episodio_end" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label>Pegar Dublado?</label>
                    <select class="form-control" name="dublado">
                        <option value="1" selected>SIM</option>
                        <option value="0">NÃO</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Pegar Legendado?</label>
                    <select class="form-control" name="legendado">
                        <option value="1" selected>SIM</option>
                        <option value="0">NÃO</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection("js"); ?>
    <script src="<?php echo e(asset("js/jquery.fix.clone.js")); ?>"></script>
    <script src="<?php echo e(asset("js/MyScript.js")); ?>"></script>
    <script>
        let count_add = 1;
        let idthemovie;
        let idthemovie_anterior;
        function add_temporada() {
            if(idthemovie){
                $("#content_dados > #dados > div > div > div > div select[name='todas_temporadas[]']").val('0');
                let element_temporada = $("#dados > div > div > div > div > div div[name='content_temporada_vista']");
                element_temporada.append($("#content_temporada").html());
                element_temporada.find("input").each(function (index, element){
                    $(element).attr("name",$(element).attr("name")+"_"+idthemovie+"[]");
                });
                element_temporada.find("select").each(function (index, element){
                    $(element).attr("name",$(element).attr("name")+"_"+idthemovie+"[]");
                });
            }else{
                Swal.fire(
                    "Adicionar o themovie antes",
                    "Você não adicionou o themovie",
                    'warning'
                );
            }

        }

        function remove_temporada() {
            $("#dados > div > div > div > div > div div[name='content_temporada_vista'] > div.row").last().remove();
        }

        function removertr(tr) {
            $(tr).remove();
        }

        function add() {
            let link = $("#content_dados > #dados > div > div > div > input[name='link[]']").val();
            idthemovie = $("#content_dados > #dados > div > div > div > input[name='id_themovie[]']").val();
            $("tbody").append("<tr id='tr-"+count_add+"'><td><p>"+link+"<p><div id='add-"+count_add+"' hidden></div></td><td id='status-"+idthemovie+"' style='background-color: red'></td><td><button type='button' class='btn btn-danger' onclick='removertr(\"#tr-"+count_add+"\")'><i class='fas fa-trash'></i></button></td></tr>");
            $("#content_dados > #dados > div").clone().appendTo("#add-"+count_add);
            // $("#dados > div > div > div > div > div div[name='content_temporada_vista']").clone().appendTo("#add-"+count_add);
            count_add++;
            limpar_campos();
        }
        let timer;
        function preparar_progresso() {
            timer = setInterval(pegar_progresso,1000);
        }

        function parar_progresso() {
            setTimeout(function () {
                clearInterval(timer);
            },3000);
        }
        function enviar_dados() {
            $("#btn-publica").prop("disabled",true);
            abrir_loading("Fazendo postagem... Aguarde...");
            preparar_progresso();
            $.post('<?php echo e(route("season.postar")); ?>', $("#form-publica").serialize(), function (msg) {
                var erro = false;
                Swal.close();
                if(msg.msg === "sucesso"){
                    Swal.fire("Sucesso", "Publicado com sucesso!", "success");
                    limpar_campos();
                    limpar_lista();
                    verificar_json();
                }else{
                    Swal.fire("Erro","Houve um erro! Erro: "+msg.msg,"error");
                }
                $("#btn-publica").prop("disabled",false);
                parar_progresso();
            },'json').fail(function () {
                Swal.close();
                Swal.fire("Erro","Houve um erro!","error");
                $("#btn-publica").prop("disabled",false);
                parar_progresso();
            });
        }

        function pegar_progresso() {
            $.getJSON('<?php echo e(route("season.progresso")); ?>', function (data) {
                let id = data[0];
                if(id !== 0){
                    $("#status-"+id).attr("style",'background-color:green');
                }
            });
        }

        function limpar_lista() {
            $("table > tbody > tr").remove();
        }

        let link_inicial = "";
        $("#content_dados > #dados > div > div > div > input[name='link[]']").keyup(function () {
            let valor = $(this).val();
            if (valor.length > 0) {
                if (valor !== link_inicial) {
                    abrir_loading("Buscando dados, aguarde");
                    $.ajax
                    ({
                        type: "POST",
                        dataType: 'json',
                        url: "<?php echo e(route('season.pesquisar')); ?>",
                        data: {_token: "<?php echo e(csrf_token()); ?>", link: valor}
                    }).done(function (data) {
                        idthemovie = data.themovie.id;
                        $("#content_dados > #dados > div > div > div > input[name='id_themovie[]']").val(data.themovie.id);
                        $("#content_dados > #dados > div > div > div > input[name='nome_themovie[]']").val(data.themovie.nome);
                        $("#themovie_pesquisa_manual").attr("href","https://www.themoviedb.org/search?query="+encodeURIComponent(data.serie)+"&language=pt-BR");
                        if(data.themovie.name){
                            $("#content_dados > #dados > div > div > div > input[name='serie_name[]']").val(data.themovie.name);
                        }else{
                            $("#content_dados > #dados > div > div > div > input[name='serie_name[]']").val(data.serie);
                        }
                        Swal.close();
                    }).fail(function () {
                        Swal.close();
                    });
                    link_inicial = valor;
                }
            }
        });

        $("#content_dados > #dados > div > div > div > input[name='link_themovie[]']").keyup(function () {
            var valor = $(this).val();
            if(valor.length > 0){
                var matches = /[0-9].*[0-9]/g.exec(valor);
                $("#content_dados > #dados > div > div > div > input[name='id_themovie[]']").val(matches[0]);
                $("#content_dados > #dados > div > div > div > input[name='nome_themovie[]']").val("Colocado manualmente, verifique o link para saber o nome");
                idthemovie = matches[0];
            }
        });

        function limpar_campos() {
            $("#content_dados > #dados > div > div > div > input[name='link_themovie[]']").val("");
            $("#content_dados > #dados > div > div > div > input[name='link[]']").val("");
            $("#content_dados > #dados > div > div > div > input[name='id_themovie[]']").val("");
            $("#content_dados > #dados > div > div > div > input[name='serie_name[]']").val("");
            $("#dados > div > div > div > div > div div[name='content_temporada_vista'] > div.row").remove();
            $("#json_textarea").val("");
            // $("#dados > div > div > div > div > div div[name='content_temporada_vista']").append($("#content_temporada").html());
        }
        function verificar_json() {
            $.ajax
            ({
                type: "POST",
                dataType: 'json',
                url: "<?php echo e(route('serie.json.result')); ?>",
                data: {_token: "<?php echo e(csrf_token()); ?>"}
            }).done(function (data) {
                    $("#json_textarea").val(data.result);
            }).fail(function () {

            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/screens/season/create-season.blade.php ENDPATH**/ ?>