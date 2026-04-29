<div>

    <!-- Header avec timer et progression -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4><i class="bi bi-clipboard"></i> {{ $evaluation->title }}</h4>
                    <p class="text-muted mb-0">{{ $questions->count() }} questions</p>
                </div>
                <div class="col-md-6 text-end">

                        <div class="time-spent" wire:poll.1s="updateTimeSpent">
                            <i class="bi bi-hourglass-split"></i> 
                            <span id="time-spent-display">{{ $formattedTimeSpent }}</span>
                            <small class="text-muted ms-2">/ {{ !empty($evaluation->duration) ? formateTime($evaluation->duration*60)  : "Illimité" }} </small>
                        </div>


                Temps restant : {{!empty($evaluation->duration) ? formateTime($timeRemaining) : "Illimité" }}

                    <div class="mt-2">
                        <small class="text-muted">Question 
                            <span>{{ $currentQuestionIndex + 1 }}</span> / {{ $questions->count() }}
                        </small>
                    </div>
                </div>
            </div>
            <!-- Barre de progression -->
            <div class="progress mt-3">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                     role="progressbar" 
                     style="width: {{ $questions->count() > 0 ? round((($currentQuestionIndex + 1) / $questions->count()) * 100, 2) : 0 }}%">
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de l'évaluation -->
    <form wire:submit.prevent="submit">
   {{-- Le mode de notation :  {{$scoring_mode}} --}}
        <!-- Questions -->
        <div id="questions-container">
            @php
                $currentQuestion = $questions[$currentQuestionIndex] ?? null;
            @endphp
            @if($currentQuestion)
                <div class="question-card card active fade-in" 
                     wire:key="question-{{ $currentQuestion->id }}">
                    <div class="card-body">
                        <!-- Indicateur de chargement -->
                        <div wire:loading.attr="nextQuestion,previousQuestion,goToQuestion" 
                             style="display: none;">
                            <div class="d-flex align-items-center py-3">
                                <div class="spinner-border text-primary me-3" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-arrow-repeat me-2"></i>
                                    Chargement de la question...
                                </p>
                            </div>
                        </div>
                        
                        <!-- Contenu de la question -->
                        <div wire:loading.remove.attr="nextQuestion,previousQuestion,goToQuestion">
                            <h5 class="card-title">
                                <span class="badge bg-primary me-2">{{ $currentQuestionIndex + 1 }}</span>
                                ({{ $currentQuestion->getTypeLabel() }}) {{ $currentQuestion->question_text }}
                                @if($scoring_mode == "points")
                                    <small class="text-muted">( {{ $currentQuestion->points }} points)</small>
                                @endif
                            </h5>
                            
                            <div class="answers-container mt-3">
                                @if($currentQuestion->type === 'text')
                                    <!-- Question de type texte -->
                                    <div class="form-group">
                                        <label for="text-answer-{{ $currentQuestion->id }}" class="form-label">
                                            <i class="bi bi-pencil-square"></i> Votre réponse :
                                        </label>
                                        <textarea 
                                            id="text-answer-{{ $currentQuestion->id }}"
                                            class="form-control" 
                                            rows="4" 
                                            placeholder="Tapez votre réponse ici..."
                                            wire:model.live="currentAnswer"
                                            wire:input.debounce.500ms="selectTextAnswer($event.target.value)">{{ $currentAnswer ?? '' }}</textarea>
                                    </div>
                                @else
                                    <!-- Questions à choix (unique, multiple, trouver l'intrus) -->
                                    @foreach ($currentQuestion->answers as $answerIndex => $answer)
                                        <div class="answer-option p-3 mb-2 border rounded" 
                                             wire:click="selectAnswer({{ $answer->id }})"
                                             wire:key="answer-{{ $answer->id }}"
                                             @class([
                                                 'selected' => $currentQuestion->type === 'multiple_choice' 
                                                     ? $this->isAnswerSelected($answer->id) 
                                                     : (($currentAnswer ?? null) == $answer->id)
                                             ])>
                                             
                                             <div class="container">
                                                 <div class="row">
                                                     <div class="col-1">
                                                        @if($currentQuestion->type === 'multiple_choice')
                                                            <!-- Checkboxes pour choix multiple -->
                                                            <input class="form-check-input m-2 p-3" type="checkbox" 
                                                               name="answers[{{ $currentQuestion->id }}][]" 
                                                               value="{{ $answer->id }}" 
                                                               id="answer-{{ $currentQuestion->id }}-{{ $answerIndex }}"
                                                               {{ $this->isAnswerSelected($answer->id) ? 'checked' : '' }}
                                                               wire:click.stop="selectAnswer({{ $answer->id }})">
                                                        @else
                                                            <!-- Radio buttons pour choix unique et trouver l'intrus -->
                                                            <input class="form-check-input m-2 p-3" type="radio" 
                                                               name="answers[{{ $currentQuestion->id }}]" 
                                                               value="{{ $answer->id }}" 
                                                               id="answer-{{ $currentQuestion->id }}-{{ $answerIndex }}"
                                                               {{ ($currentAnswer ?? null) == $answer->id ? 'checked' : '' }}
                                                               wire:click.stop="selectAnswer({{ $answer->id }})">
                                                        @endif
                                                     </div>
                                                     <div class="col">
                                                      <label class="form-check-label w-100 cursor-pointer p-3" 
                                                           for="answer-{{ $currentQuestion->id }}-{{ $answerIndex }}">
                                                            <span class="answer-letter me-2 fw-bold">
                                                                {{ chr(65 + $answerIndex) }})
                                                            </span>
                                                            {{ $answer->answer_text }}
                                                            {{-- @if($currentQuestion->type === 'find_intruder')
                                                                @if(!$answer->is_correct)
                                                                    <span class="badge bg-warning ms-2">
                                                                        <i class="bi bi-exclamation-triangle"></i> Intrus possible
                                                                    </span>
                                                                @endif
                                                            @endif --}}
                                                        </label>
                                                     </div>
                                                 </div>
                                             </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($currentQuestion->type === 'multiple_choice')
                                        <div class="alert alert-info mt-3">
                                            <i class="bi bi-info-circle"></i> 
                                            <small>Vous pouvez sélectionner plusieurs réponses pour cette question.</small>
                                        </div>
                                    @elseif($currentQuestion->type === 'find_intruder')
                                        <div class="alert alert-warning mt-3">
                                            <i class="bi bi-target"></i> 
                                            <small>Trouvez l'intrus parmi les propositions ci-dessus.</small>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Navigation -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" 
                                id="prev-btn"
                                class="btn btn-outline-secondary" 
                                wire:click="previousQuestion"
                                {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}>
                            <span class="btn-content">
                                <i class="bi bi-arrow-left"></i> Précédent
                            </span>
                            <span wire:loading.attr="previousQuestion"  class="btn-loading" style="display: none;">
                                <span class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </span>
                                Chargement...
                            </span>
                        </button>
                    </div>
                    <div class="col-md-6 text-end">
                        @if($currentQuestionIndex === $questions->count() - 1)
                            <button type="submit" id="submit-btn" class="btn btn-success">
                                <span class="btn-content">
                                    <i class="bi bi-check-circle"></i> Soumettre l'évaluation
                                </span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Chargement...</span>
                                    </span>
                                    Soumission...
                                </span>
                            </button>
                        @else
                            <button type="button" 
                                    id="next-btn"
                                    class="btn btn-primary" 
                                    wire:click="nextQuestion">
                                <span class="btn-content">
                                    Suivant <i class="bi bi-arrow-right"></i>
                                </span>
                                <span   wire:loading.attr="NextQuestion"  class="btn-loading" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Chargement...</span>
                                    </span>
                                    Chargement...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Résumé des réponses -->
        <div class="card mt-4">
            <div class="card-header">
                <h6>
                <i class="bi bi-list-check"></i> Relire mes réponses
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($questions as $index => $question)
                        <div class="col-md-2 col-sm-3 col-4 mb-2">
                            <div class="answer-indicator text-center p-2 border rounded @class(['answered' => isset($answers[$question->id])])" 
                                 wire:click="goToQuestion({{ $index }})">
                                <strong>{{ $index + 1 }}</strong>
                                <div class="small">
                                    <span class="status">
                                        {{ isset($answers[$question->id]) ? 'Répondue' : 'Non répondue' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        {{ count($answers) }} / {{ $questions->count() }} questions répondues
                    </small>
                </div>
            </div>
        </div>
    </form>

    <style>
        .question-card {
            margin-bottom: 2rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .question-card.active {
            border-color: var(--primary-color) 
            box-shadow: 0 0 15px rgba(13, 110, 253, 0.15);
        }
        .answer-option {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .answer-option:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        .answer-option.selected {
            background-color: #e7f3ff;
            border-color: var(--primary-color)
        }
        .timer {
            font-size: 1.5rem;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .timer.warning {
            color: #ffc107;
        }
        .timer.danger {
            color: #dc3545;
        }
        .time-spent {
            font-size: 1rem;
            font-weight: 500;
            color: #6c757d;
        }
        .progress-bar {
            height: 8px;
            transition: width 0.5s ease;
        }
        .answer-indicator {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .answer-indicator:hover {
            transform: translateY(-2px);
        }
        .answer-indicator.answered {
            background-color: #d1f2eb;
            border-color: #198754;
            color: #198754;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
                
        /* Animations supplémentaires */
        .answer-option {
            position: relative;
            overflow: hidden;
        }
        
        .answer-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }
        
        .answer-option:hover::before {
            left: 100%;
        }
        
        /* Amélioration de la progression */
        .progress {
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .progress-bar {
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Animation de pulsation pour les questions actives */
/*
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
            }
        }
        */
        
        .question-card.active {
           /* animation: pulse 2s infinite;*/
        }
        
        /* Effet de ripple pour les clics */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Gérer l'état de chargement des boutons
        function setButtonLoading(buttonId, isLoading) {
            const button = document.getElementById(buttonId);
            if (!button) return;
            
            const content = button.querySelector('.btn-content');
            const loading = button.querySelector('.btn-loading');
            
            if (isLoading) {
                button.disabled = true;
                if (content) content.style.display = 'none';
                if (loading) loading.style.display = 'inline';
            } else {
                button.disabled = false;
                if (content) content.style.display = 'inline';
                if (loading) loading.style.display = 'none';
            }
        }

        // Écouter les événements Livewire
        Livewire.on('questionChanged', (index) => {
            // Activer le chargement sur les boutons de navigation
            setButtonLoading('prev-btn', true);
            setButtonLoading('next-btn', true);
            
            // Animation de transition
            const cards = document.querySelectorAll('.question-card');
            cards.forEach(card => {
                card.style.display = 'none';
                card.classList.remove('active');
            });
            
            const currentCard = document.querySelector(`[wire\\:key="question-{{ $questions[$currentQuestionIndex]->id ?? '' }}"]`);
            if (currentCard) {
                setTimeout(() => {
                    currentCard.style.display = 'block';
                    currentCard.classList.add('active');
                    
                    // Réactiver les boutons après la transition
                    setTimeout(() => {
                        setButtonLoading('prev-btn', false);
                        setButtonLoading('next-btn', false);
                    }, 200);
                }, 300);
            } else {
                // Réactiver les boutons si pas de carte trouvée
                setButtonLoading('prev-btn', false);
                setButtonLoading('next-btn', false);
            }
        });

        Livewire.on('answerSelected', (data) => {
            // Mettre à jour l'indicateur de réponse
            const indicators = document.querySelectorAll('.answer-indicator');
            indicators.forEach((indicator, index) => {
                if (index === data.questionIndex) {
                    indicator.classList.add('answered');
                    indicator.querySelector('.status').textContent = 'Répondue';
                }
            });
        });

        Livewire.on('showConfirmation', (unanswered) => {
            Swal.fire({
                title: 'Questions non répondues',
                html: `
                    <p>Attention : vous avez <strong>${unanswered}</strong> question(s) sans réponse.</p>
                    <p class="text-muted">Voulez-vous quand même soumettre l'évaluation ?</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, soumettre',
                cancelButtonText: 'Annuler',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    setButtonLoading('submit-btn', true);
                    @this.call('confirmSubmit');
                }
            });
        });

        Livewire.on('showSuccess', (message) => {
            Swal.fire({
                title: 'Succès!',
                text: message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });

        Livewire.on('showError', (message) => {
            Swal.fire({
                title: 'Erreur!',
                text: message,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        });

        Livewire.on('timeWarning', (timeRemaining) => {
            Swal.fire({
                title: 'Temps presque écoulé!',
                html: `
                    <p>Il ne vous reste que <strong>${Math.floor(timeRemaining / 60)}:${String(timeRemaining % 60).padStart(2, '0')}</strong></p>
                    <p class="text-muted">Veuillez terminer votre évaluation rapidement.</p>
                `,
                icon: 'warning',
                timer: 5000,
                showConfirmButton: false,
                toast: true,
                position: 'top'
            });
        });

        Livewire.on('evaluationSubmitted', () => {
            Swal.fire({
                title: 'Évaluation soumise!',
                text: 'Votre évaluation est en cours de traitement...',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        });

        // Navigation au clavier avec confirmation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                // Vérifier si le bouton suivant est actif et non désactivé
                const nextBtn = document.getElementById('next-btn');
                if (nextBtn && !nextBtn.disabled) {
                    setButtonLoading('next-btn', true);
                    @this.call('nextQuestion');
                }
            } else if (e.key === 'ArrowLeft') {
                // Vérifier si le bouton précédent est actif et non désactivé
                const prevBtn = document.getElementById('prev-btn');
                if (prevBtn && !prevBtn.disabled) {
                    setButtonLoading('prev-btn', true);
                    @this.call('previousQuestion');
                }
            } else if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn && !submitBtn.disabled) {
                    setButtonLoading('submit-btn', true);
                    @this.call('submit');
                }
            }
        });

        // Améliorer l'expérience de sélection des réponses
        document.addEventListener('click', function(e) {
            if (e.target.closest('.answer-option')) {
                const option = e.target.closest('.answer-option');
                
                // Ajouter un effet de sélection visuel
                option.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    option.style.transform = 'scale(1)';
                }, 150);
            }
        });

        // Animation de progression fluide
        function updateProgressBar() {
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                const currentWidth = parseFloat(progressBar.style.width);
                const targetWidth = {{ $questions->count() > 0 ? round((($currentQuestionIndex + 1) / $questions->count()) * 100, 2) : 0 }};
                
                if (currentWidth !== targetWidth) {
                    progressBar.style.width = targetWidth + '%';
                }
            }
        }

        // Mettre à jour la progression lors du changement de question
        Livewire.hook('message.processed', (message, component) => {
            if (message.component.method === 'nextQuestion' || 
                message.component.method === 'previousQuestion' || 
                message.component.method === 'goToQuestion') {
                updateProgressBar();
            }
        });
    </script>
</div>
