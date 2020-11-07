<div class="form-group <?php echo e($divClass ?? ""); ?>">
    <div class="card <?php echo e($class ?? "bg-primary"); ?> ">
        <a title="<?php echo e(__("modeladminlang::default.click_to_expand")); ?>" data-toggle="collapse" class="<?php echo e(empty($open) ? "collapsed":""); ?>"
           <?php echo e(empty($open) ? "":"aria-expanded='true'"); ?> href="#<?php echo e($id??"collapse1"); ?>">
            <div class="card-header">
                <h4 class="card-title <?php echo e($class_title ?? ""); ?>"><i class="<?php echo e($icon ?? ""); ?>"></i> <?php echo e($title ?? ""); ?>

                </h4>
                <?php echo empty($header) ? "":$header; ?>

            </div>
        </a>
        <div id="<?php echo e($id ?? "collapse1"); ?>"
             class="collapse <?php echo e(empty($open) ? "":"show"); ?>">
            <div class="card-body">
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>
</div>


<?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/layouts/componentes/painel-collapse.blade.php ENDPATH**/ ?>