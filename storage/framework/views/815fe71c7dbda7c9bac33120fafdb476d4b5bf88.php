<?php
    $mensagens = \App\Utils\MensagensFodas::mensagem_aleatoria();
    $mensagem = \App\Utils\MensagensFodas::$mensagens[$mensagens]
?>
<?php $__env->startSection('title', 'AdminLTE'); ?>
<?php $__env->startSection('content_header'); ?>
    <h1>Seja Bem-Vindo <?php echo e(auth()->user()->name); ?></h1>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-text-width"></i>
                        Mensagens fodas para você
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <blockquote>
                        <p><?php echo e($mensagem['text']); ?></p>
                        <small><?php echo e($mensagem["by"]); ?></small>
                    </blockquote>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
    <div class="form-group">
        <img src="<?php echo e(asset("img/logo/mestre.jpg")); ?>" class="img-fluid">
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/home.blade.php ENDPATH**/ ?>