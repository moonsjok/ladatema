<?php $__env->startSection('page-title', 'LADATEMA GROUP — Portail Officiel'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-3 group-viewport">
    <!-- BANNIÈRE COMPACTE DU GROUPE -->
    <div class="hero-compact mb-4 text-center">
        <div class="pill-badge mb-2 mx-auto">
            <i class="bi bi-shield-check text-warning"></i>
            <span>LADATEMA GROUP — Hub Pluridisciplinaire</span>
        </div>
        <h1 class="fw-bold text-white mb-2 fs-2">
            L'Excellence au Service de Votre Réussite
        </h1>
        <p class="text-light opacity-90 mx-auto mb-0 fs-6" style="max-width: 750px;">
            Cabinet d'expertise comptable, audit, conseil financier, analyse boursière & ressources humaines. Sélectionnez votre portail ci-dessous :
        </p>
    </div>

    <!-- LES 4 BOUTONS / CARTES PORTAILS HARMONISÉES -->
    <div class="row g-3">
        <!-- PORTAIL 1: LADATEMA RESEARCH -->
        <div class="col-md-6 col-lg-3">
            <div class="portal-card-unified">
                <div>
                    <div class="portal-logo-box mb-3">
                        <img src="<?php echo e(asset('images/LOGO_LADATEMA_SARL.png')); ?>" alt="LADATEMA Research" class="portal-logo-img">
                    </div>
                    <div class="text-primary fw-semibold fs-7 mb-2">Recherche & Formations</div>
                    <p class="text-muted fs-7 mb-3">
                        Formations professionnelles en ligne, cours BRVM et espace d'apprentissage.
                    </p>
                </div>
                <div>
                    <a href="<?php echo e(route('welcome')); ?>" class="btn-brand-action">
                        <span>Accéder au Portail</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- PORTAIL 2: LADA BOURSE -->
        <div class="col-md-6 col-lg-3">
            <div class="portal-card-unified">
                <div>
                    <div class="portal-logo-box mb-3">
                        <img src="<?php echo e(asset('images/lada-bourse.png')); ?>" alt="Lada Bourse" class="portal-logo-img">
                    </div>
                    <div class="text-primary fw-semibold fs-7 mb-2">Marchés & BRVM</div>
                    <p class="text-muted fs-7 mb-3">
                        Analyses boursières, cours en temps réel, obligations et opportunités SGI.
                    </p>
                </div>
                <div>
                    <a href="<?php echo e(asset('lada/bourse.html')); ?>" target="_blank" rel="noopener noreferrer" class="btn-brand-action">
                        <span>Accéder au Portail</span>
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- PORTAIL 3: LADA RH -->
        <div class="col-md-6 col-lg-3">
            <div class="portal-card-unified">
                <div>
                    <div class="portal-logo-box mb-3">
                        <img src="<?php echo e(asset('images/lada-rh.png')); ?>" alt="Lada RH" class="portal-logo-img">
                    </div>
                    <div class="text-primary fw-semibold fs-7 mb-2">Ressources Humaines</div>
                    <p class="text-muted fs-7 mb-3">
                        Recrutement de talents, gestion de paie, audit social et conseil RH.
                    </p>
                </div>
                <div>
                    <a href="<?php echo e(asset('lada/rh.html')); ?>" target="_blank" rel="noopener noreferrer" class="btn-brand-action">
                        <span>Accéder au Portail</span>
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- PORTAIL 4: LADA FINANCE -->
        <div class="col-md-6 col-lg-3">
            <div class="portal-card-unified">
                <div>
                    <div class="portal-logo-box mb-3">
                        <img src="<?php echo e(asset('images/lada-finance.png')); ?>" alt="Lada Finance" class="portal-logo-img">
                    </div>
                    <div class="text-primary fw-semibold fs-7 mb-2">Comptabilité & Audit</div>
                    <p class="text-muted fs-7 mb-3">
                        Assistance comptable, fiscalité, commissariat aux comptes et création d'entreprise.
                    </p>
                </div>
                <div>
                    <a href="<?php echo e(asset('lada/finance.html')); ?>" target="_blank" rel="noopener noreferrer" class="btn-brand-action">
                        <span>Accéder au Portail</span>
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- INFOS CONTACT RAPIDE EN BAS -->
    <div class="d-flex flex-wrap align-items-center justify-content-between bg-white rounded-4 p-3 mt-3 border shadow-sm fs-7 text-muted">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill text-primary"></i>
            <span>Besoin d'un conseil spécifique ? Nos experts sont à votre écoute.</span>
        </div>
        <div class="d-flex gap-3">
            <a href="<?php echo e(route('contact.form')); ?>" class="text-primary fw-bold text-decoration-none">
                <i class="bi bi-envelope-fill me-1"></i> Formulaire de Contact
            </a>
            <span class="text-light">|</span>
            <a href="<?php echo e(route('nos.services')); ?>" class="text-secondary fw-bold text-decoration-none">
                <i class="bi bi-briefcase-fill me-1"></i> Nos Prestations
            </a>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest.index', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\ladatema\resources\views/group.blade.php ENDPATH**/ ?>