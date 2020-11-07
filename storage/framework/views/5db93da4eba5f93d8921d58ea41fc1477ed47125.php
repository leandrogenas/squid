<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="<?php echo e($icon ?? ""); ?>"></i>
                <?php echo e($title ?? ""); ?>

            </h3>
            <?php echo e($box_header ?? ""); ?>

        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php echo e($slot); ?>

        </div>
        <div class="card-footer">
            <?php echo e($box_footer ?? ""); ?>

        </div>
        <!-- /.card-body -->
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/layouts/componentes/box.blade.php ENDPATH**/ ?>