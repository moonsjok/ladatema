@extends('layouts.authenticated.owners.index')

@section('title', 'Modifier la question')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0">
                            <i class="bi bi-pencil"></i> Édition de question
                        </h4>
                    </div>
                    <a href="{{ route('evaluations.show', $question->evaluation_id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour à l'évaluation
                    </a>
                </div>

                <!-- Formulaire d'édition -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">

                            {{ $question->question_text }}

                        </h5>
                    </div>
                    <div class="card-body">
                        @livewire('question-edit-form', ['evaluationId' => $question->evaluation_id, 'questionId' => $question->id])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Écouter les événements Livewire pour afficher les alertes SweetAlert
        document.addEventListener('livewire:init', () => {
            // Succès de sauvegarde
            Livewire.on('questionUpdated', (message) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: message || 'La question a été mise à jour avec succès.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    timer: 2000,
                    timerProgressBar: true
                }).then((result) => {
                    if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                        window.location.href =
                            '{{ route('evaluations.show', $question->evaluation_id) }}';
                    }
                });
            });

            // Erreur de sauvegarde
            Livewire.on('questionUpdateError', (message) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: message || 'Une erreur est survenue lors de la mise à jour.',
                    confirmButtonText: 'OK'
                });
            });

            // Confirmation avant de quitter sans sauvegarder
            window.addEventListener('beforeunload', (e) => {
                const livewireComponent = Livewire.find('question-edit-form');
                if (livewireComponent && livewireComponent.isDirty) {
                    e.preventDefault();
                    e.returnValue =
                        'Vous avez des modifications non sauvegardées. Voulez-vous vraiment quitter?';
                    return e.returnValue;
                }
            });
        });
    </script>
@endpush
