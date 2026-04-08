@extends('layouts.authenticated.students.index')

@section('page-title', 'Course Viewer')

@section('dashboard-content')

    {{-- 🔔 Bandeau haut si pas Chrome --}}
    <div id="browser-banner"
        style="display:none; background-color:#fff3cd; color:#856404; border:1px solid #ffeeba; padding:10px 20px; margin-bottom: 15px; border-radius: 5px;">
        <img src="https://www.google.com/chrome/static/images/favicons/favicon-96x96.png" width="24" class="me-2"
            alt="Chrome">
        Pour une meilleure expérience, nous vous recommandons d'utiliser <strong>Google Chrome</strong>.
        <a href="https://www.google.com/chrome/" target="_blank" class="text-decoration-underline">Télécharger</a>
    </div>

    <div class="d-flex justify-content-between p-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    @livewire('authenticated.course-viewer', [
        'courseId' => $course->id,
        'chapterId' => $chapterId ?? null,
        'type' => request()->query('type') ?? null,
        'id' => request()->query('id') ?? null,
    ])

    <style>
        video {
            width: 100% !important;
        }
    </style>

    {{-- Modal Bootstrap (non utilisé ici car on utilise SweetAlert) --}}
    <div class="modal fade" id="browserModal" tabindex="-1" aria-labelledby="browserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="browserModalLabel">Navigateur recommandé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <img src="https://www.google.com/chrome/static/images/favicons/favicon-96x96.png" width="24"
                        class="me-2">
                    Pour une meilleure expérience, nous vous recommandons d'utiliser <strong>Google Chrome</strong>.
                    <br>
                    <a href="https://www.google.com/chrome/" target="_blank">Télécharger Google Chrome</a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">J'ai compris</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const userAgent = navigator.userAgent.toLowerCase();
                
                // Détection améliorée des navigateurs
                const isChrome = /chrome/.test(userAgent) && !/edge|opr|brave|firefox|safari/.test(userAgent);
                const isFirefox = /firefox/.test(userAgent) && !/seamonkey/.test(userAgent);
                const isEdge = /edg/.test(userAgent);
                const isSafari = /safari/.test(userAgent) && !/chrome/.test(userAgent);
                const isOpera = /opera/.test(userAgent) || /opr/.test(userAgent);
                const isBrave = /brave/.test(userAgent);
                
                // Vérifier si c'est un navigateur moderne
                const isModernBrowser = isChrome || isFirefox || isEdge || (isSafari && /version\/([0-9]+)/.test(userAgent) && parseInt(/version\/([0-9]+)/.exec(userAgent)[1]) >= 14);
                
                // Informations de débogage
                console.log('Détection navigateur:', {
                    userAgent: userAgent,
                    isChrome: isChrome,
                    isFirefox: isFirefox,
                    isEdge: isEdge,
                    isSafari: isSafari,
                    isOpera: isOpera,
                    isBrave: isBrave,
                    isModernBrowser: isModernBrowser
                });

                if (!isModernBrowser) {
                    // 👉 Affiche le bandeau pour navigateurs non supportés
                    const browserName = isFirefox ? 'Firefox' : 
                                     isEdge ? 'Edge' : 
                                     isSafari ? 'Safari' : 
                                     isOpera ? 'Opera' : 
                                     isBrave ? 'Brave' : 'Navigateur inconnu';
                    
                    document.getElementById('browser-banner').style.display = 'block';
                    
                    // 👉 Recommande Google Chrome SEULEMENT si Chrome n'est pas déjà utilisé
                    if (!localStorage.getItem('browserWarningDismissed')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Compatibilité navigateur',
                            html: `Pour une meilleure expérience, nous vous recommandons d'utiliser <b>Google Chrome</b>.<br><br>
                                   Votre navigateur actuel : <b>${browserName}</b><br><br>
                                   <a href="https://www.google.com/chrome/" target="_blank" class="btn btn-primary btn-sm me-2">
                                       <i class="bi bi-google"></i> Télécharger Chrome
                                   </a>`,
                            confirmButtonText: 'J\'ai compris',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showCancelButton: true,
                            cancelButtonText: 'Continuer avec ' + browserName,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                localStorage.setItem('browserWarningDismissed', true);
                            }
                        });
                    }
                } else {
                    // ✅ Navigateur moderne détecté - aucune alerte nécessaire
                    localStorage.removeItem('browserWarningDismissed');
                    console.log('✅ Navigateur moderne détecté:', {
                        browser: isChrome ? 'Chrome' : 
                               isFirefox ? 'Firefox' : 
                               isEdge ? 'Edge' : 
                               isSafari ? 'Safari' : 
                               isOpera ? 'Opera' : 
                               isBrave ? 'Brave' : 'Autre',
                        version: isChrome ? navigator.userAgent.match(/Chrome\/([0-9.]+)/)?.[1] : 'N/A'
                    });
                }
            });
        </script>
    @endpush

@endsection
