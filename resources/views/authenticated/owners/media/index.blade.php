@extends('layouts.authenticated.owners.index')

@section('page-title', 'Gestion des Médias - ' . ucfirst($type))

@section('dashboard-content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i
                        class="bi bi-{{ $type === 'images' ? 'image' : ($type === 'videos' ? 'play-circle' : ($type === 'pdfs' ? 'file-pdf' : 'file-text')) }}"></i>
                    Gestion des {{ ucfirst($type) }}
                </h5>
                <a href="{{ route('media.create', $type) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Ajouter
                </a>
            </div>
            <div class="card-body">
                <!-- Navigation entre types de fichiers -->
                <div class="mb-3">
                    <div class="btn-group" role="group">
                        <a href="{{ route('media.index', 'images') }}"
                            class="btn {{ $type === 'images' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-image"></i> Images
                        </a>
                        <a href="{{ route('media.index', 'videos') }}"
                            class="btn {{ $type === 'videos' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-play-circle"></i> Vidéos
                        </a>
                        <a href="{{ route('media.index', 'pdfs') }}"
                            class="btn {{ $type === 'pdfs' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-file-pdf"></i> PDFs
                        </a>
                        <a href="{{ route('media.index', 'txt_files') }}"
                            class="btn {{ $type === 'txt_files' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-file-text"></i> Textes
                        </a>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="mediaTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Aperçu</th>
                                <th>Nom</th>
                                {{-- <th>Taille</th> --}}
                                <th>Propriétaire</th>
                                {{-- <th>Date</th> --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#mediaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('media.data', $type) }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    error: function(xhr, error, code) {
                        console.log('DataTables Error:', error);
                        console.log('Response:', xhr.responseText);
                        // Afficher un message d'erreur si DataTables échoue
                        $('#mediaTable').parent().html(
                            '<div class="alert alert-warning">' +
                            '<i class="bi bi-exclamation-triangle"></i> ' +
                            'Erreur lors du chargement des données. Veuillez réessayer.' +
                            '</div>'
                        );
                    }
                },
                columns: [{
                        data: 'preview',
                        name: 'preview',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    /* {
                         data: 'size',
                         name: 'size',
                         orderable: false,
                         searchable: false
                     },*/
                    {
                        data: 'owner',
                        name: 'owner',
                        orderable: true,
                        searchable: true
                    },
                    /*  {
                          data: 'created_at',
                          name: 'created_at'
                      },*/
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    processing: "Traitement en cours...",
                    search: "Rechercher:",
                    lengthMenu: "Afficher _MENU_ éléments",
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                    infoEmpty: "Affichage de 0 à 0 sur 0 éléments",
                    infoFiltered: "(filtré de _MAX_ éléments au total)",
                    infoPostFix: "",
                    loadingRecords: "Chargement en cours...",
                    zeroRecords: "Aucun élément à afficher",
                    emptyTable: "Aucun fichier {{ $type === 'images' ? 'image' : ($type === 'videos' ? 'vidéo' : 'document') }} trouvé",
                    paginate: {
                        first: "Premier",
                        previous: "Précédent",
                        next: "Suivant",
                        last: "Dernier"
                    },
                    aria: {
                        sortAscending: ": activer pour trier la colonne par ordre croissant",
                        sortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }
                },
                pageLength: 25,
                responsive: true,
                order: [
                    [1, 'asc']
                ], // Trier par nom par défaut
                initComplete: function(settings, json) {
                    console.log('DataTables initialisé avec succès');
                }
            });
        });

        // Fonction pour copier l'URL dans le presse-papiers
        function copyUrl(url, fileName) {
            navigator.clipboard.writeText(url).then(function() {
                // Afficher une notification de succès
                var toast = '<div class="position-fixed top-0 end-0 p-3" style="z-index: 11">' +
                    '<div class="toast show" role="alert">' +
                    '<div class="toast-header">' +
                    '<i class="bi bi-clipboard-check text-success me-2"></i>' +
                    '<strong class="me-auto">URL copiée!</strong>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="toast"></button>' +
                    '</div>' +
                    '<div class="toast-body">' +
                    'L\'URL de "' + fileName + '" a été copiée dans le presse-papiers.' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                $('body').append(toast);

                // Supprimer le toast après 3 secondes
                setTimeout(function() {
                    $('.toast').parent().remove();
                }, 3000);
            }).catch(function(err) {
                console.error('Erreur lors de la copie: ', err);
                alert('Erreur lors de la copie de l\'URL');
            });
        }
    </script>
@endpush
