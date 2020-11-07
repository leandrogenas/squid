<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent("layouts.componentes.box",["title"=>"Trocar Imagem Filme","icon"=> "fas fa-sync-alt"]); ?>
        <?php echo e(html()->form("post","")->id("form")->open()); ?>

        <input id="progress" type="hidden" name="progress" value="progress-<?php echo e(mt_rand(1,30)); ?>">
        <div class="form-group">
            <label>Cole os ID's de postagem</label>
            <textarea name="postagem_id" placeholder="Separados por , Ex: 1010,12850" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <input type="checkbox" name="imdb" checked> Usar IMDB?
        </div>
        <?php echo e(html()->form()->close()); ?>

        <div class="form-group">
            <?php $__env->startComponent("layouts.componentes.table"); ?>
                <?php $__env->slot("table_head"); ?>
                    <tr>
                        <th>Publicados</th>
                    </tr>
                <?php $__env->endSlot(); ?>
            <?php echo $__env->renderComponent(); ?>
        </div>
        <div id="show" style="display: none;" class="form-group">
            <h3 style="color: red">Aguarde está sendo publicado</h3>
        </div>
        <div class="form-group">
            <button onclick="publicar()" type="button" class="btn btn-primary">Atualizar</button>
        </div>
    <?php echo $__env->renderComponent(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection("js"); ?>
    <script>

        let timer;
        var filme_anterior = "";

        function publicar() {
            $("#show").show();
            timer = setInterval(function () {
                pegar_progresso();
            }, 1000);
            enviar_dados();
        }

        function pegar_progresso() {
            $.getJSON('<?php echo e(route("filmes.progresso")); ?>/?p=' + $("#progress").val(), function (data) {
                var filme_publicado = data[1];
                if (filme_publicado !== 0) {
                    if (filme_publicado !== filme_anterior) {
                        $("tbody").append("<tr><td>" + filme_publicado + "</td></tr>");
                        filme_anterior = filme_publicado;
                    }
                }
            });
        }

        function enviar_dados() {
            $.post('<?php echo e(route("filmes.trocaimagem.post")); ?>', $("#form").serialize(), function (msg) {
                console.log(msg);
                if (msg.msg === "sucesso") {
                    Swal.fire("Sucesso", "Publicado com sucesso!", "success");
                } else {
                    Swal.fire("Erro", "Houve um erro! Erro: " + msg.msg, "error");
                }
                clearInterval(timer);
                $("#show").hide();
                setTimeout(function () {
                    pegar_progresso();
                }, 3000);
            }, 'json').fail(function () {
                Swal.fire("Erro", "Houve um erro!", "error");
                clearInterval(timer);
                $("#div-status").hide();
                $("#show").hide();
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/screens/movies/trocaimagem-filme.blade.php ENDPATH**/ ?>