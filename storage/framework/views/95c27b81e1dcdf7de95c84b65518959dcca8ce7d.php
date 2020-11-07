<div class="form-group">
    <div class="<?php echo e($class ??"icheck-primary"); ?> d-inline">
        <input  value="<?php echo e($value ?? ""); ?>"  type="checkbox"  <?php echo e(empty($function ) ? "":"checked=checked"); ?> name="<?php echo e($name ?? ""); ?>" id="<?php echo e($id ?? "checkboxPrimary1"); ?>" >
        <label for="<?php echo e($id ?? "checkboxPrimary1"); ?>">
            <?php echo e($slot); ?>

        </label>
    </div>
</div>

<?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/layouts/componentes/checkbox.blade.php ENDPATH**/ ?>