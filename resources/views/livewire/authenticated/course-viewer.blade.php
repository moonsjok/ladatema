<div id="course-viewer-root">
    <!-- Hero / En-tête du cours -->
    <div class="card border-0 rounded-4 shadow-sm bg-gradient-primary text-white p-4 mb-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="text-white-50 small fw-semibold text-uppercase tracking-wider mb-1">
                    <i class="bi bi-journal-bookmark me-1"></i> Module de Formation
                </div>
                <h2 class="fw-bold mb-1 text-white">{{ $selectedCourse->title }}</h2>
                @if ($selectedCourse->description)
                    <div class="text-white-50 text-truncate max-w-2xl small mt-1">
                        {!! strip_tags($selectedCourse->description) !!}
                    </div>
                @endif
            </div>

            <!-- Boutons Rapides de Navigation entre Cours -->
            <div class="d-flex gap-2 shrink-0">
                @if ($previousCourse)
                    <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm text-primary fw-semibold" 
                            wire:click="goToPreviousCourse" 
                            title="{{ $previousCourse->title }}">
                        <i class="bi bi-chevron-left me-1"></i> Cours Précédent
                    </button>
                @endif
                @if ($nextCourse)
                    <button class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm text-dark fw-semibold" 
                            wire:click="goToNextCourse" 
                            title="{{ $nextCourse->title }}">
                        Cours Suivant <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                @endif
            </div>
        </div>

        @if ($totalChapters > 0)
            <!-- Barre de progression global du cours -->
            <div class="mt-4 pt-2 border-top border-white-10">
                <div class="d-flex justify-content-between align-items-center mb-1 text-white-50 small">
                    <span>Progression du cours</span>
                    <span class="fw-bold text-white">{{ $progressPercent }}% (Chapitre {{ $currentIndex }}/{{ $totalChapters }})</span>
                </div>
                <div class="progress bg-white-20" style="height: 6px; border-radius: 10px; background-color: rgba(255,255,255,0.2);">
                    <div class="progress-bar bg-warning rounded-pill" role="progressbar" 
                         style="width: {{ $progressPercent }}%;" 
                         aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        @endif
    </div>

    <!-- Interface Principale avec Sidebar & Lecteur -->
    <div class="row g-4">
        
        <!-- Navigation Mobile (Sélecteur compact de Chapitres) -->
        <div class="col-12 d-block d-lg-none">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <button class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center rounded-3 py-2 px-3" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#mobileChapterCollapse" 
                            aria-expanded="false">
                        <span class="fw-semibold text-truncate">
                            <i class="bi bi-list-nested me-2"></i>
                            @if($selectedChapter)
                                Chapitre {{ $selectedChapter->numero }} : {{ $selectedChapter->title }}
                            @else
                                Sélectionner un chapitre
                            @endif
                        </span>
                        <span class="badge bg-primary rounded-pill ms-2">{{ $currentIndex }}/{{ $totalChapters }}</span>
                    </button>
                    
                    <div class="collapse mt-3" id="mobileChapterCollapse">
                        <div class="list-group list-group-flush rounded-3 border">
                            @foreach ($chapters as $chapter)
                                <button wire:click="selectChapter({{ $chapter->id }})"
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#mobileChapterCollapse"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2.5 px-3 {{ $selectedChapter?->id === $chapter->id ? 'active fw-bold' : '' }}">
                                    <span class="text-truncate me-2">
                                        <span class="badge bg-secondary me-2">{{ $chapter->numero }}</span>
                                        {{ $chapter->title }}
                                    </span>
                                    @if($selectedChapter?->id === $chapter->id)
                                        <i class="bi bi-check-circle-fill text-warning"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Chapitres (Desktop Sticky) -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-journal-text text-primary me-2"></i>Chapitres
                        </h5>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                            {{ $totalChapters }} au total
                        </span>
                    </div>
                </div>
                
                <div class="card-body px-3 py-2">
                    <div class="list-group list-group-flush custom-chapter-list pe-1" style="max-height: 65vh; overflow-y: auto;">
                        @if ($chapters->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                Aucun chapitre disponible pour ce cours.
                            </div>
                        @else
                            @foreach ($chapters as $chapter)
                                @php $isActive = $selectedChapter?->id === $chapter->id; @endphp
                                <button wire:click="selectChapter({{ $chapter->id }})"
                                        class="list-group-item list-group-item-action rounded-3 mb-1.5 border-0 py-3 px-3 transition-all d-flex align-items-center justify-content-between {{ $isActive ? 'bg-primary text-white shadow-sm fw-semibold' : 'hover-bg-light text-dark' }}">
                                    <div class="d-flex align-items-center text-truncate me-2">
                                        <span class="badge rounded-circle p-2 me-3 d-flex align-items-center justify-content-center {{ $isActive ? 'bg-white text-primary' : 'bg-light text-secondary border' }}" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                            {{ $chapter->numero }}
                                        </span>
                                        <span class="text-truncate">{{ $chapter->title }}</span>
                                    </div>
                                    @if($isActive)
                                        <i class="bi bi-play-circle-fill text-warning fs-5"></i>
                                    @endif
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu du Chapitre Sélectionné (Lecteur) -->
        <div class="col-lg-8" id="chapter-reader-card">
            @if ($chapters->isNotEmpty() && $selectedChapter)
                
                <!-- Barre de Navigation Haut du Chapitre -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <!-- Bouton Précédent (Chapitre ou Cours) -->
                        @if ($currentIndex > 1)
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" wire:click="previousChapter">
                                <i class="bi bi-arrow-left me-1.5"></i> Chapitre Précédent
                            </button>
                        @elseif ($previousCourse)
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold" wire:click="goToPreviousCourse">
                                <i class="bi bi-arrow-left-circle me-1.5"></i> Cours Précédent
                            </button>
                        @else
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 disabled" disabled>
                                <i class="bi bi-arrow-left me-1.5"></i> Chapitre Précédent
                            </button>
                        @endif

                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small fw-semibold">
                            Chapitre {{ $selectedChapter->numero }} / {{ $totalChapters }}
                        </span>

                        <!-- Bouton Suivant (Chapitre ou Cours) -->
                        @if ($currentIndex < $totalChapters)
                            <button class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" wire:click="nextChapter">
                                Chapitre Suivant <i class="bi bi-arrow-right ms-1.5"></i>
                            </button>
                        @elseif ($nextCourse)
                            <button class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold text-dark" wire:click="goToNextCourse">
                                Cours Suivant <i class="bi bi-arrow-right-circle ms-1.5"></i>
                            </button>
                        @else
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 disabled" disabled>
                                Chapitre Suivant <i class="bi bi-arrow-right ms-1.5"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Carte du Contenu Principal -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <div class="d-flex align-items-center gap-2 mb-1 text-primary small fw-bold">
                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1">N° {{ $selectedChapter->numero }}</span>
                            <span>CHAPITRE ACTIF</span>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">{{ $selectedChapter->title }}</h3>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <!-- Remplace le paragraphe <p> par un conteneur div valide et responsive -->
                        <div class="chapter-body-content">
                            {!! $selectedChapter->content !!}
                        </div>
                    </div>
                </div>

                <!-- Barre de Navigation Bas du Chapitre -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        @if ($currentIndex > 1)
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" wire:click="previousChapter">
                                <i class="bi bi-arrow-left me-1.5"></i> Chapitre Précédent
                            </button>
                        @elseif ($previousCourse)
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold" wire:click="goToPreviousCourse">
                                <i class="bi bi-arrow-left-circle me-1.5"></i> Cours Précédent
                            </button>
                        @else
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 disabled" disabled>
                                <i class="bi bi-arrow-left me-1.5"></i> Chapitre Précédent
                            </button>
                        @endif

                        @if ($currentIndex < $totalChapters)
                            <button class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" wire:click="nextChapter">
                                Chapitre Suivant <i class="bi bi-arrow-right ms-1.5"></i>
                            </button>
                        @elseif ($nextCourse)
                            <button class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold text-dark" wire:click="goToNextCourse">
                                Cours Suivant <i class="bi bi-arrow-right-circle ms-1.5"></i>
                            </button>
                        @else
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 disabled" disabled>
                                Chapitre Suivant <i class="bi bi-arrow-right ms-1.5"></i>
                            </button>
                        @endif
                    </div>
                </div>

            @else
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <div class="card-body">
                        <i class="bi bi-exclamation-triangle text-warning display-4 d-block mb-3"></i>
                        <h4 class="fw-bold text-dark">Aucun chapitre disponible</h4>
                        <p class="text-muted mb-4">Ce cours n'a pas encore de chapitre publié.</p>
                        @if ($previousCourse || $nextCourse)
                            <div class="d-flex justify-content-center gap-3">
                                @if ($previousCourse)
                                    <button class="btn btn-outline-primary rounded-pill px-4" wire:click="goToPreviousCourse">
                                        <i class="bi bi-arrow-left me-2"></i>Cours Précédent
                                    </button>
                                @endif
                                @if ($nextCourse)
                                    <button class="btn btn-primary rounded-pill px-4" wire:click="goToNextCourse">
                                        Cours Suivant <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>
    <style>
        /* Styles typographiques et de médias responsives pour le contenu du chapitre */
        .chapter-body-content {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #2d3748;
        }

        .chapter-body-content p {
            margin-bottom: 1.25rem;
        }

        .chapter-body-content img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 12px;
            margin: 1.5rem 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .chapter-body-content video {
            width: 100% !important;
            height: auto !important;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            background-color: #000;
        }

        .custom-video-wrapper {
            position: relative;
            background-color: #ffffff;
            transition: all 0.3s ease;
        }

        .video-controls-toolbar {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .chapter-body-content iframe {
            max-width: 100% !important;
            border-radius: 12px;
            margin: 1.5rem 0;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        /* Rendu 16:9 responsive pour conteneurs iFrames / Vidéos */
        .chapter-body-content .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
            margin: 1.5rem 0;
        }

        .chapter-body-content .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            margin: 0;
        }

        .chapter-body-content blockquote {
            border-left: 4px solid #3b82f6;
            background-color: #f8fafc;
            padding: 1rem 1.25rem;
            border-radius: 0 8px 8px 0;
            font-style: italic;
            margin: 1.5rem 0;
            color: #475569;
        }

        .chapter-body-content table {
            width: 100% !important;
            margin: 1.5rem 0;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
        }

        .chapter-body-content table th,
        .chapter-body-content table td {
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
        }

        .chapter-body-content table th {
            background-color: #f1f5f9;
            font-weight: 600;
        }

        /* Personnalisation de la liste des chapitres */
        .custom-chapter-list::-webkit-scrollbar {
            width: 4px;
        }

        .custom-chapter-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .transition-all {
            transition: all 0.2s ease-in-out;
        }
    </style>

@script
    <script>
        (function() {
            let audioContextMap = new WeakMap();

            function initVideoPlayerEnhancements() {
                const container = document.querySelector('.chapter-body-content');
                if (!container) return;

                const videos = container.querySelectorAll('video');
                videos.forEach((video) => {
                    if (video.dataset.enhanced === 'true') return;
                    video.dataset.enhanced = 'true';

                    // Configuration de chargement optimisé
                    video.setAttribute('preload', 'metadata');
                    video.setAttribute('playsinline', 'true');
                    video.style.width = '100%';
                    video.style.borderRadius = '12px';
                    video.style.backgroundColor = '#000';

                    // Si la vidéo n'est pas déjà dans un wrapper responsive, la wrapper
                    let wrapper = video.closest('.custom-video-wrapper');
                    if (!wrapper) {
                        wrapper = document.createElement('div');
                        wrapper.className = 'custom-video-wrapper my-4 rounded-4 shadow-sm border bg-white p-3';
                        video.parentNode.insertBefore(wrapper, video);
                        wrapper.appendChild(video);
                    }

                    // Création du panneau d'outils audio & vidéo (Boost Volume 100% à 300% & Vitesse)
                    const controlBar = document.createElement('div');
                    controlBar.className = 'video-controls-toolbar mt-3 p-3 bg-light rounded-3 border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3';

                    controlBar.innerHTML = `
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1.5 small fw-semibold">
                                <i class="bi bi-volume-up-fill me-1"></i> Amplificateur Son
                            </span>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Amplificateur sonore">
                                <button type="button" class="btn btn-primary btn-boost active" data-boost="100">100%</button>
                                <button type="button" class="btn btn-outline-primary btn-boost" data-boost="150">150%</button>
                                <button type="button" class="btn btn-outline-primary btn-boost" data-boost="200">200% 🔥</button>
                                <button type="button" class="btn btn-outline-primary btn-boost" data-boost="300">300% 🚀</button>
                            </div>
                            <span class="badge bg-light text-primary border rounded-pill px-2 py-1 small boost-indicator">
                                Gain : 100% (Normal)
                            </span>
                        </div>

                        <div class="d-flex align-items-center flex-wrap gap-2 ms-md-auto">
                            <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1.5 small fw-semibold">
                                <i class="bi bi-speedometer2 me-1"></i> Vitesse
                            </span>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Vitesse de lecture">
                                <button type="button" class="btn btn-outline-secondary btn-speed" data-speed="0.75">0.75x</button>
                                <button type="button" class="btn btn-secondary btn-speed active" data-speed="1.0">1.0x</button>
                                <button type="button" class="btn btn-outline-secondary btn-speed" data-speed="1.25">1.25x</button>
                                <button type="button" class="btn btn-outline-secondary btn-speed" data-speed="1.5">1.5x</button>
                                <button type="button" class="btn btn-outline-secondary btn-speed" data-speed="2.0">2.0x</button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-dark rounded-circle btn-fullscreen ms-1" title="Plein écran">
                                <i class="bi bi-fullscreen"></i>
                            </button>
                        </div>
                    `;

                    wrapper.appendChild(controlBar);

                    // Initialisation AudioContext et GainNode pour cette vidéo
                    let audioData = {
                        audioCtx: null,
                        source: null,
                        gainNode: null
                    };

                    function setupAudioContext() {
                        if (!audioData.audioCtx) {
                            try {
                                const AudioContext = window.AudioContext || window.webkitAudioContext;
                                audioData.audioCtx = new AudioContext();
                                audioData.source = audioData.audioCtx.createMediaElementSource(video);
                                audioData.gainNode = audioData.audioCtx.createGain();
                                audioData.source.connect(audioData.gainNode);
                                audioData.gainNode.connect(audioData.audioCtx.destination);
                                audioContextMap.set(video, audioData);
                            } catch (e) {
                                console.warn('Web Audio API non disponible:', e);
                            }
                        }
                        if (audioData.audioCtx && audioData.audioCtx.state === 'suspended') {
                            audioData.audioCtx.resume();
                        }
                    }

                    // Écouteur pour débloquer l'AudioContext lors de la lecture
                    video.addEventListener('play', () => {
                        setupAudioContext();
                    });

                    // Gestion des boutons de Boost de volume (100% -> 300%)
                    const boostButtons = controlBar.querySelectorAll('.btn-boost');
                    const boostIndicator = controlBar.querySelector('.boost-indicator');

                    boostButtons.forEach(btn => {
                        btn.addEventListener('click', () => {
                            setupAudioContext();
                            const boostValue = parseInt(btn.dataset.boost, 10);
                            const gainMultiplier = boostValue / 100;

                            if (audioData.gainNode && audioData.audioCtx) {
                                audioData.gainNode.gain.setValueAtTime(gainMultiplier, audioData.audioCtx.currentTime);
                            }

                            boostButtons.forEach(b => {
                                b.classList.remove('active', 'btn-primary');
                                b.classList.add('btn-outline-primary');
                            });
                            btn.classList.remove('btn-outline-primary');
                            btn.classList.add('active', 'btn-primary');

                            if (boostValue > 100) {
                                boostIndicator.className = 'badge bg-warning text-dark border border-warning rounded-pill px-2.5 py-1 small fw-bold';
                                boostIndicator.innerHTML = `🔥 Boost : ${boostValue}%`;
                            } else {
                                boostIndicator.className = 'badge bg-light text-primary border rounded-pill px-2 py-1 small';
                                boostIndicator.innerHTML = `Gain : 100% (Normal)`;
                            }
                        });
                    });

                    // Gestion des boutons de Vitesse de lecture (0.75x -> 2.0x)
                    const speedButtons = controlBar.querySelectorAll('.btn-speed');
                    speedButtons.forEach(btn => {
                        btn.addEventListener('click', () => {
                            const speed = parseFloat(btn.dataset.speed);
                            video.playbackRate = speed;

                            speedButtons.forEach(b => {
                                b.classList.remove('active', 'btn-secondary');
                                b.classList.add('btn-outline-secondary');
                            });
                            btn.classList.remove('btn-outline-secondary');
                            btn.classList.add('active', 'btn-secondary');
                        });
                    });

                    // Gestion du bouton Plein Écran
                    const fullscreenBtn = controlBar.querySelector('.btn-fullscreen');
                    if (fullscreenBtn) {
                        fullscreenBtn.addEventListener('click', () => {
                            if (video.requestFullscreen) {
                                video.requestFullscreen();
                            } else if (video.webkitRequestFullscreen) {
                                video.webkitRequestFullscreen();
                            } else if (video.msRequestFullscreen) {
                                video.msRequestFullscreen();
                            }
                        });
                    }
                });
            }

            // Exécution au chargement
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initVideoPlayerEnhancements);
            } else {
                initVideoPlayerEnhancements();
            }

            // Relancement lors du changement de chapitre Livewire
            $wire.on('chapter-changed', () => {
                const card = document.getElementById('chapter-reader-card');
                if (card) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                setTimeout(initVideoPlayerEnhancements, 150);
            });

            // MutationObserver pour observer les modifications dynamiques du contenu
            const observer = new MutationObserver(() => {
                initVideoPlayerEnhancements();
            });

            document.addEventListener('DOMContentLoaded', () => {
                const root = document.querySelector('#course-viewer-root');
                if (root) {
                    observer.observe(root, { childList: true, subtree: true });
                }
            });
        })();
    </script>
@endscript
</div>

