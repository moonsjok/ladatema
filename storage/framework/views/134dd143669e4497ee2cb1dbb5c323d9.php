<style>
    .logo {
        display: inline-flex;
        align-items: center;
        /* Aligne verticalement les éléments */
        gap: 0;
        /* Supprime les espaces entre les caractères */
    }

    .logo>img {
        width: 130px;
        height: auto;
    }
</style>

<a href="<?php echo e(route('group.home')); ?>" class="logo text-decoration-none">
    <img class="img-fluid" src="<?php echo e(asset('images/LOGO_LADATEMA_SARL.png')); ?>" load="lazy">
</a>
<?php /**PATH D:\www\ladatema\resources\views/layouts/shares/logo.blade.php ENDPATH**/ ?>