<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-3"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="<?php echo e(route('welcome')); ?>">
            <?php echo $__env->make('layouts.shares.logo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </a>

        <div class="collapse navbar-collapse justify-content-center" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('group.home') ? 'active fw-bold text-primary' : ''); ?>" href="<?php echo e(route('group.home')); ?>">
                        <i class="bi bi-building"></i> Groupe Lada
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('welcome') ? 'active fw-bold text-primary' : ''); ?>" href="<?php echo e(route('welcome')); ?>">
                        <i class="bi bi-journal-bookmark-fill"></i> Lada Research
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('nos.services') ? 'active fw-bold text-primary' : ''); ?>" href="<?php echo e(route('nos.services')); ?>">
                        <i class="bi bi-gear-fill"></i> Nos services
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('guest.formationsList') || request()->routeIs('guest.formations*') ? 'active fw-bold text-primary' : ''); ?>" href="<?php echo e(route('guest.formationsList')); ?>">
                        <i class="bi bi-mortarboard-fill"></i> Formations
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarPolesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid-fill"></i> Nos Portails
                    </a>
                    <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="navbarPolesDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2" href="<?php echo e(asset('lada/bourse.html')); ?>" target="_blank" rel="noopener noreferrer">
                                <span><i class="bi bi-graph-up-arrow text-success me-2"></i> Lada Bourse</span>
                                <i class="bi bi-box-arrow-up-right text-muted fs-7 ms-2"></i>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2" href="<?php echo e(asset('lada/rh.html')); ?>" target="_blank" rel="noopener noreferrer">
                                <span><i class="bi bi-people-fill text-info me-2"></i> Lada RH</span>
                                <i class="bi bi-box-arrow-up-right text-muted fs-7 ms-2"></i>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2" href="<?php echo e(asset('lada/finance.html')); ?>" target="_blank" rel="noopener noreferrer">
                                <span><i class="bi bi-calculator-fill text-warning me-2"></i> Lada Finance</span>
                                <i class="bi bi-box-arrow-up-right text-muted fs-7 ms-2"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('contact.form') ? 'active fw-bold text-primary' : ''); ?>" href="<?php echo e(route('contact.form')); ?>">
                        <i class="bi bi-telephone-fill"></i> Contacts
                    </a>
                </li>
            </ul>
        </div>

        <div class="d-flex align-items-center">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-primary btn-sm me-2 d-flex align-items-center">
                    <i class="bi bi-speedometer2"></i>
                    <span class="d-none d-lg-inline ms-1">Tableau de bord</span>
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="d-none d-lg-inline ms-1">Déconnexion</span>
                    </button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary btn-sm me-2 d-flex align-items-center">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span class="d-none d-lg-inline ms-1">Connexion</span>
                </a>
                <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-success btn-sm d-flex align-items-center">
                    <i class="bi bi-person-plus-fill"></i>
                    <span class="d-none d-lg-inline ms-1">Inscription</span>
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    </div>
</nav>
<?php /**PATH D:\www\ladatema\resources\views/layouts/guest/shares/navbar.blade.php ENDPATH**/ ?>