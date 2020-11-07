<?php if(config('sweetalert.local') && !empty(config('sweetalert.cdn'))): ?>
<script src="<?php echo e(asset('vendor/sweetalert/sweetalert.all.js')); ?>"></script>
<script>
    Swal.fire({
        "title":"Warning!",
        "html":`You can not use <b>cdn</b> and <b>local</b> together! <br />
        You can only use <b>cdn</b> or <b>local</b>! <br />
        If you want to use <b>cdn</b> goto <b>.env</b> and change <pre>SWEET_ALERT_LOCAL=false</pre>`,
        "type":"warning",
        "showConfirmButton":false,
        "showCloseButton":true,
        "allowEscapeKey":false,
        "allowOutsideClick":false
    });
</script>
<?php elseif(config('sweetalert.local')): ?>
<script src="<?php echo e(asset('vendor/sweetalert/sweetalert.all.js')); ?>"></script>
    <?php if(Session::has('alert.config')): ?>
<script>
    Swal.fire(<?php echo Session::pull('alert.config'); ?>);
</script>
    <?php endif; ?>
<?php elseif(!is_null(config('sweetalert.cdn'))): ?>
<script src="<?php echo e(config('sweetalert.cdn')); ?>"></script>
    <?php if(Session::has('alert.config')): ?>
<script>
    Swal.fire(<?php echo Session::pull('alert.config'); ?>);
</script>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\SyncWeb\resources\views/vendor/sweetalert/alert.blade.php ENDPATH**/ ?>