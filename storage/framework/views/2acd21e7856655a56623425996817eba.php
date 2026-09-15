<script>
    function showSweetAlert(type, message) {
        Swal.fire({
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 5000
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        <?php if(session('success')): ?>
            showSweetAlert('success', '<?php echo e(session('success')); ?>');
        <?php elseif(session('status')): ?>
            showSweetAlert('success', '<?php echo e(session('status')); ?>');
        <?php elseif(session('error')): ?>
            showSweetAlert('error', '<?php echo e(session('error')); ?>');
        <?php endif; ?>
    });
</script>
<script>
    <?php if($errors->any()): ?>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                text: '<?php echo e($error); ?>'
            });
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php if(session('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '<?php echo e(session('success')); ?>'
        });
    <?php endif; ?>

    <?php if(session('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '<?php echo e(session('error')); ?>'
        });
    <?php endif; ?>

    <?php if(session('info')): ?>
        Swal.fire({
            icon: 'info',
            title: 'Information',
            text: '<?php echo e(session('info')); ?>'
        });
    <?php endif; ?>

    <?php if(session('warning')): ?>
        Swal.fire({
            icon: 'warning',
            title: 'Attention',
            text: '<?php echo e(session('warning')); ?>'
        });
    <?php endif; ?>
</script>
<?php /**PATH D:\www\ladatema\resources\views/layouts/partials/_swal_alert_messages.blade.php ENDPATH**/ ?>