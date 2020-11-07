<div class="box <?php echo e($type ?? "box-default"); ?> <?php echo e(empty($close) ? "":" collapsed-box"); ?>">
    <div class="box-header with-border">
        <i class="<?php echo e($icon ?? ""); ?>"></i>
        <h3 class="box-title"><?php echo e($title ?? ""); ?></h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo e(empty($close) ? "fa-minus":"fa-plus"); ?>"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <?php echo e($slot); ?>

    </div>
    <!-- /.box-body -->
</div>
<?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/layouts/componentes/box-collapse.blade.php ENDPATH**/ ?>