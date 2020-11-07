<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent("layouts.componentes.box",["title"=>"Atualizar Séries","icon"=> "fas fa-video"]); ?>
        <div class="form-group">
            <label>Link do Site</label>
            <input class="form-control" id="link_site">
        </div>
        <div class="form-group">
            <p>Cole o link aqui para pegar o id</p>
            <label>Link ThemovieDB</label>
            <input class="form-control" id="link_themovie">
        </div>
        <div class="form-group">
            <label>ID ThemovieDB</label>
            <input readonly class="form-control" id="id_themovie">
            <input placeholder="Nome encontrado themoviedb" class="form-control" readonly id="nome_themovie">
        </div>
        <div class="form-group">
            <label>ID IMDB</label>
            <input class="form-control" id="id_imdb">
            <input placeholder="Nome encontrado imdb" class="form-control" readonly id="nome_imdb">
        </div>
        <div class="form-group">
            <a id="imdb_pesquisa_manual" target="_blank" href="https://www.imdb.com/find?q=" class="btn btn-primary">Pesquisar IMDB manual</a>
            <a id="themovie_pesquisa_manual" class="btn btn-warning" target="_blank" href="https://www.themoviedb.org/search?query=&language=pt-BR">Pesquisa TheMovieDB manual</a>
        </div>
        <hr>
        <?php $__env->startComponent("layouts.componentes.painel-collapse",["title"=>"Postagens para editar","class"=>"panel-primary"]); ?>
            <div class="form-group text-center">
                <label>Postagens para editar</label>
            </div>
            <?php $__currentLoopData = \App\Enums\Sites::get_movies_sites_active(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-group">
                    <label>ID da postagem no site: <?php echo e($site); ?></label>
                    <input class="form-control" name="<?php echo e($site); ?>" id="<?php echo e($site); ?>" placeholder="ID da postagem">
                    <input class="form-control" id="<?php echo e($site); ?>_nome" readonly placeholder="Nome encontrado">
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->renderComponent(); ?>
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
                <label>Séries Publicados</label>
            </div>
            <div class="form-group">
                <ul id="lista-filme"></ul>
            </div>
        </div>
        <div class="form-group">
            <div class="form-group">
                <strong>Publicando nos sites:</strong>
            </div>
            <div class="row">
                <?php $__currentLoopData = \App\Enums\Sites::get_movies_sites_active(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-1">
                        <p><?php echo e($site); ?></p>
                        <span class="dot" id="<?php echo e($site); ?>_status"></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="form-group">
            <label>Lista de Séries para publicar</label>
        </div>
        <?php echo e(html()->form("post",route("filmes.store"))->id("form-publica")->open()); ?>

        <input id="progress" type="hidden" name="progress" value="progress-<?php echo e(mt_rand(1,30)); ?>">
        <input type="hidden" name="serie" value="1">
        <div class="form-group table-responsive">
            <?php $__env->startComponent("layouts.componentes.table"); ?>
                <?php $__env->slot("table_head"); ?>
                    <tr>
                        <th>Link do Site</th>
                        <th>Remover</th>
                    </tr>
                <?php $__env->endSlot(); ?>
            <?php echo $__env->renderComponent(); ?>
        </div>
        <div class="form-group">
            <label>Publicar nos Sites</label>
            <?php ($id = 1); ?>
            <?php $__currentLoopData = \App\Enums\Sites::get_movies_sites_active(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__env->startComponent("layouts.componentes.checkbox",["id"=>"checkbox".$id,"value"=>$site,"function"=>"checked","name"=>"sites[]"]); ?>
                    <?php echo e($site); ?>

                <?php echo $__env->renderComponent(); ?>
                <?php ($id++); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php echo e(html()->form()->close()); ?>

        <?php $__env->slot("box_footer"); ?>
            <div class="form-group">
                <button id="btn-publica" onclick="publicar()" class="btn btn-success" type="button">Atualizar</button>
            </div>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection("js"); ?>
    <script>
        let ultimo_site_alterado = "";
        function alterar_status_sites(site, color) {
            if (site !== ultimo_site_alterado) {
                if (site !== 0) {
                    $("#" + site+"_status").css("background-color", color);
                } else {
                    $("span.dot").css("background-color", "#bbb");
                }
                ultimo_site_alterado = site;
            }
        }

        function limpar_lista() {
            $("table > tbody > tr").remove();
        }

        let link_inicial = "";
        $("#link_site").keyup(function () {
            var valor = $(this).val();
            if (valor.length > 0) {
                if (valor !== link_inicial) {
                    abrir_loading("Buscando dados, aguarde");
                    $.ajax
                    ({
                        type: "POST",
                        dataType: 'json',
                        url: "<?php echo e(route('serie.pesquisa.update')); ?>",
                        data: {_token: "<?php echo e(csrf_token()); ?>", link: valor}
                    }).done(function (data) {
                        $("#id_themovie").val(data.themovie.id);
                        $("#id_imdb").val(data.imdb.id);
                        $("#nome_themovie").val(data.themovie.nome);
                        $("#nome_imdb").val(data.imdb.nome);
                        $("#imdb_pesquisa_manual").attr("href","https://www.imdb.com/find?q="+encodeURIComponent(data.themovie.nome));
                        $("#themovie_pesquisa_manual").attr("href","https://www.themoviedb.org/search?query="+encodeURIComponent(data.serie)+"&language=pt-BR");
                        <?php $__currentLoopData = \App\Enums\Sites::get_movie_sites(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        $("#<?php echo e($site); ?>").val(data.post_site.<?php echo e($site); ?>.id);
                        $("#<?php echo e($site); ?>_nome").val(data.post_site.<?php echo e($site); ?>.titulo);
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        Swal.close();
                    }).fail(function () {
                        Swal.close();
                    });
                    link_inicial = valor;
                }
            }
        });
        $("#link_themovie").keyup(function () {
            var valor = $(this).val();
            if (valor.length > 0) {
                var matches = /[0-9].*[0-9]/g.exec(valor);
                $("#id_themovie").val(matches[0]);
                $("#nome_themovie").val("Colocado manualmente, verifique o link para saber o nome");
            }
        });

        function adicionar() {
            var link = $("#link_site").val();
            var link_the_movie = $("#id_themovie").val();
            var link_imdb = $("#id_imdb").val();
            var total_tr = $("tbody > tr").length;
            var html_sites = "";
            <?php $__currentLoopData = \App\Enums\Sites::get_movie_sites(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                var post_id_<?php echo e($site); ?> = $("#<?php echo e($site); ?>").val();
                html_sites += "<input type='hidden' name='<?php echo e($site); ?>' value='"+post_id_<?php echo e($site); ?>+"'>";
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            total_tr++;
            $("table > tbody").append(" <tr id='" + total_tr + "'> <td><input type='hidden' name='link_site[]' value='" + link + "'> <input type='hidden' name='id_themovies[]' value='" + link_the_movie + "'><input type='hidden' name='is_cinema[]' value='false'> <input type='hidden' name='id_imdbs[]' value='" + link_imdb + "'>" + link + html_sites + "</td><td><button type='button' onclick=\"remover('tr#" + total_tr + "')\" class='btn btn-danger'><i class='far fa-times-circle'> Remover</i></button></td></tr>");
            limpar_campos();
        }

        function remover(tr_id) {
            $(tr_id).remove();
        }

        function limpar_campos() {
            $("#link_site").val("");
            $("#link_themovie").val("");
            $("#id_themovie").val("");
            $("#id_imdb").val("");
            $("#nome_themovie").val("");
            $("#nome_imdb").val("");
            <?php $__currentLoopData = \App\Enums\Sites::get_movie_sites(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            $("#<?php echo e($site); ?>").val("");
            $("#<?php echo e($site); ?>_nome").val("");
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        }
        let timer;
        var filme_anterior = "";
        function publicar() {
            if ($("tbody > tr").length <= 0) {
                Swal.fire("Campos Vázio", "É necessário pelo menos um link adicionado", "warning");
            } else {
                $("#div-status").show();
                $("#btn-publica").prop("disabled",true);
                timer = setInterval(function () {
                    pegar_progresso(false);
                }, 1000);
                enviar_dados();
            }
        }

        function pegar_progresso(erro) {
            $.getJSON('<?php echo e(route("filmes.progresso")); ?>/?p=' + $("#progress").val(), function (data) {
                $("#p-status").text(data[0]);
                var filme_publicado = data[1];
                var site_publicando = data[2];
                if(filme_publicado !== 0){
                    $("#lista-publicada").show();
                    if(filme_publicado !== filme_anterior){
                        $("#lista-filme").append("<li>"+filme_publicado+"</li>");
                        filme_anterior = filme_publicado;
                    }
                }
                if(erro){
                    alterar_status_sites(ultimo_site_alterado,"red");
                }else{
                    alterar_status_sites(site_publicando,"green");
                }
            });
        }

        function enviar_dados() {
            $.post('<?php echo e(route("serie.update")); ?>', $("#form-publica").serialize(), function (msg) {
                var erro = false;
                if(msg.msg === "sucesso"){
                    Swal.fire("Sucesso", "Publicado com sucesso!", "success");
                    limpar_lista();
                }else{
                    Swal.fire("Erro","Houve um erro! Erro: "+msg.msg,"error");
                    erro = true;
                }
                $("#btn-publica").prop("disabled",false);
                clearInterval(timer);
                $("#div-status").hide();
                setTimeout(function () {
                    pegar_progresso(erro);
                },3000);
            },'json').fail(function () {
                Swal.fire("Erro","Houve um erro!","error");
                clearInterval(timer);
                $("#div-status").hide();
                $("#btn-publica").prop("disabled",false);
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/screens/series/update-serie.blade.php ENDPATH**/ ?>