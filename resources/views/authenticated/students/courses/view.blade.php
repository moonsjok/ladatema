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
                
                // Détection précise et robuste des navigateurs
                const isEdge = /edg\/|edge\/|edga\/|edgios\//.test(userAgent);
                const isOpera = /opr\/|opera\/|opios\//.test(userAgent);
                const isVivaldi = /vivaldi/.test(userAgent);
                const isSamsung = /samsungbrowser/.test(userAgent);
                const isYandex = /yabrowser/.test(userAgent);
                const isBrave = (navigator.brave && typeof navigator.brave.isBrave === 'function') || /brave/.test(userAgent);
                const isTor = /torbrowser|tor\/|\btor\b/.test(userAgent);
                const isSeamonkey = /seamonkey/.test(userAgent);
                const isFirefox = /firefox|fxios/.test(userAgent) && !isSeamonkey;
                const isIE = /msie|trident/.test(userAgent);
                const isChromium = /chromium/.test(userAgent);
                
                // Google Chrome (Desktop, Android, iOS CriOS)
                // Le User-Agent de Chrome contient "chrome" et "safari", mais PAS edge/opr/vivaldi/samsungbrowser/yabrowser/brave/chromium.
                let isChrome = (/chrome\/|crios\//.test(userAgent)) && !isEdge && !isOpera && !isVivaldi && !isSamsung && !isYandex && !isBrave && !isChromium;
                
                // Support de navigator.userAgentData (API moderne pour Google Chrome)
                if (navigator.userAgentData && Array.isArray(navigator.userAgentData.brands)) {
                    const brands = navigator.userAgentData.brands.map(b => b.brand.toLowerCase());
                    const isEdgeBrand = brands.some(b => b.includes('microsoft edge') || b.includes('edge'));
                    const isOperaBrand = brands.some(b => b.includes('opera'));
                    const isChromeBrand = brands.some(b => b.includes('google chrome'));
                    if (isChromeBrand && !isEdgeBrand && !isOperaBrand && !isBrave) {
                        isChrome = true;
                    }
                }

                // Safari (contient "safari", mais PAS chrome/crios/edg/opr/samsungbrowser/yabrowser/etc.)
                const isSafari = /safari/.test(userAgent) && !/chrome|crios|android|edg|opr|samsungbrowser|yabrowser/.test(userAgent);
                
                // Détection mobile
                const isMobile = (navigator.userAgentData && navigator.userAgentData.mobile) || /mobile|android|iphone|ipad|phone/.test(userAgent);
                
                // Extraire les versions
                let browserVersion = 'Inconnue';
                let browserName = 'Navigateur inconnu';
                
                if (isChrome) {
                    browserName = 'Google Chrome';
                    const chromeMatch = userAgent.match(/(?:chrome|crios)\/([0-9.]+)/);
                    browserVersion = chromeMatch ? chromeMatch[1] : 'Inconnue';
                } else if (isFirefox) {
                    browserName = 'Mozilla Firefox';
                    const firefoxMatch = userAgent.match(/(?:firefox|fxios)\/([0-9.]+)/);
                    browserVersion = firefoxMatch ? firefoxMatch[1] : 'Inconnue';
                } else if (isEdge) {
                    browserName = 'Microsoft Edge';
                    const edgeMatch = userAgent.match(/(?:edg|edge|edga|edgios)\/([0-9.]+)/);
                    browserVersion = edgeMatch ? edgeMatch[1] : 'Inconnue';
                } else if (isSafari) {
                    browserName = 'Safari';
                    const safariMatch = userAgent.match(/version\/([0-9.]+)/);
                    browserVersion = safariMatch ? safariMatch[1] : 'Inconnue';
                } else if (isOpera) {
                    browserName = 'Opera';
                    const operaMatch = userAgent.match(/(?:opr|opera|opios)\/([0-9.]+)/);
                    browserVersion = operaMatch ? operaMatch[1] : 'Inconnue';
                } else if (isBrave) {
                    browserName = 'Brave';
                    const braveMatch = userAgent.match(/chrome\/([0-9.]+)/);
                    browserVersion = braveMatch ? braveMatch[1] : 'Inconnue';
                } else if (isVivaldi) {
                    browserName = 'Vivaldi';
                    const vivaldiMatch = userAgent.match(/vivaldi\/([0-9.]+)/);
                    browserVersion = vivaldiMatch ? vivaldiMatch[1] : 'Inconnue';
                } else if (isSamsung) {
                    browserName = 'Samsung Internet';
                    const samsungMatch = userAgent.match(/samsungbrowser\/([0-9.]+)/);
                    browserVersion = samsungMatch ? samsungMatch[1] : 'Inconnue';
                } else if (isTor) {
                    browserName = 'Tor Browser';
                    browserVersion = 'Basé sur Firefox';
                } else if (isIE) {
                    browserName = 'Internet Explorer';
                    const ieMatch = userAgent.match(/msie ([0-9.]+)/) || userAgent.match(/rv:([0-9.]+)/);
                    browserVersion = ieMatch ? ieMatch[1] : 'Inconnue';
                } else if (isChromium) {
                    browserName = 'Chromium';
                    const chromiumMatch = userAgent.match(/chromium\/([0-9.]+)/);
                    browserVersion = chromiumMatch ? chromiumMatch[1] : 'Inconnue';
                }
                
                // Recommander Chrome uniquement si ce n'est pas Google Chrome
                const shouldRecommendChrome = !isChrome;
                
                // Informations de débogage
                console.log('Détection navigateur corrigée:', {
                    userAgent: userAgent,
                    browserName: browserName,
                    browserVersion: browserVersion,
                    isMobile: isMobile,
                    isChrome: isChrome,
                    isFirefox: isFirefox,
                    isEdge: isEdge,
                    isSafari: isSafari,
                    isOpera: isOpera,
                    isBrave: isBrave,
                    isVivaldi: isVivaldi,
                    isSamsung: isSamsung,
                    isTor: isTor,
                    isIE: isIE,
                    isChromium: isChromium,
                    shouldRecommendChrome: shouldRecommendChrome
                });

                // Afficher une alerte SEULEMENT si ce n'est pas Chrome
                if (shouldRecommendChrome) {
                    // Affiche le bandeau pour navigateurs non Chrome
                    const banner = document.getElementById('browser-banner');
                    if (banner) banner.style.display = 'block';
                    
                    // Message personnalisé selon le navigateur
                    let recommendationMessage = `Pour une meilleure expérience, nous vous recommandons d'utiliser <b>Google Chrome</b>.<br><br>
                                                   Votre navigateur actuel : <b>${browserName} ${browserVersion}</b>`;
                    
                    // Messages spécifiques pour certains navigateurs
                    if (isIE) {
                        recommendationMessage = `<b>Internet Explorer n'est plus supporté</b> et présente des risques de sécurité.<br><br>
                                               Pour votre sécurité et une meilleure expérience, veuillez utiliser <b>Google Chrome</b>.<br><br>
                                               Votre navigateur : <b>${browserName} ${browserVersion}</b>`;
                    } else if (isSafari && parseFloat(browserVersion) < 14) {
                        recommendationMessage = `Votre version de Safari est trop ancienne.<br><br>
                                               Pour une meilleure expérience, veuillez mettre à jour Safari ou utiliser <b>Google Chrome</b>.<br><br>
                                               Votre navigateur : <b>${browserName} ${browserVersion}</b>`;
                    }
                    
                    // Affiche SweetAlert SEULEMENT si Chrome n'est pas déjà utilisé et pas déjà dismissé
                    if (!localStorage.getItem('browserWarningDismissed')) {
                        Swal.fire({
                            icon: isIE ? 'error' : 'warning',
                            title: isIE ? 'Navigateur obsolète' : 'Compatibilité navigateur',
                            html: `${recommendationMessage}<br><br>
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
                    // Google Chrome détecté - masquer le bandeau et aucune alerte nécessaire
                    const banner = document.getElementById('browser-banner');
                    if (banner) banner.style.display = 'none';
                    localStorage.removeItem('browserWarningDismissed');
                    console.log('Google Chrome détecté - aucune alerte affichée:', {
                        browser: browserName,
                        version: browserVersion,
                        mobile: isMobile ? 'Oui' : 'Non'
                    });
                }
            });
        </script>
    @endpush

@endsection
