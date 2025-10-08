@extends('layouts.authenticated.owners.index')

@section('page-title', 'Liste des Étudiants')

@section('dashboard-content')
    <h1 class="mb-4">Liste des Étudiants</h1>
    <p class="text-muted fw-bold">Cette page vous permet de voir les étudiants ayant effectué une souscription et ceux qui
        n'ont pas encore souscrit.</p>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="studentTab">
        <li class="nav-item">
            <a class="nav-link active fw-bold" id="no-subscription-tab" data-bs-toggle="tab" href="#no-subscription">
                Étudiants sans Souscription ({{ $countOf_studentsWithoutSubscriptions }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-bold" id="subscribed-tab" data-bs-toggle="tab" href="#subscribed">
                Étudiants avec Souscription ({{ $countOf_studentsWithSubscriptions }})
            </a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Étudiants sans souscription -->
        <div class="tab-pane fade show active" id="no-subscription">
            <table id="noSubscriptionTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom complet</th>
                        <th>Contacts</th>
                        <th>Dernière connexion</th>
                        {{-- <th>Action</th> --}}
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Étudiants avec souscription -->
        <div class="tab-pane fade" id="subscribed">
            <table id="subscribedTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Référence Paiement</th>
                        <th>Nom étudiant</th>
                        <th>Formation</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#noSubscriptionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('subscriptions.students') }}?type=without_subscriptions',
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json",
                },
                order: [
                    [0, 'asc']
                ], // Trie par défaut sur le nom
                columns: [{
                        data: 'nom_complet',
                        name: 'nom_complet',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'contacts',
                        name: 'contacts',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'last_login',
                        name: 'last_login',
                        orderable: true,
                        searchable: false
                    }
                    /*  {
                          data: 'action',
                          name: 'action',
                          orderable: false,
                          searchable: false
                      }*/
                ]
            });

            $('#subscribedTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('subscriptions.students') }}?type=with_subscriptions',
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json",
                },
                order: [
                    [3, 'desc']
                ], // Trie par défaut sur la date de création
                columns: [
                    {
                        data: 'payment_reference',
                        name: 'payment_reference',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'student',
                        name: 'student',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (type === 'display') {
                                // data is expected to be HTML safe
                                return data;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'formation_title',
                        name: 'formation_title',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.validate-subscription', function() {
                let subscriptionId = $(this).data('id');

                Swal.fire({
                    title: "Confirmer la validation",
                    text: "Voulez-vous vraiment valider cette souscription ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Oui, valider"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('subscriptions.validate', ':id') }}".replace(
                                ':id', subscriptionId),
                            type: "PUT",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Succès",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#28a745"
                                }).then(() => {
                                    // Mise à jour du DataTable après la fermeture de l'alerte
                                    $('#subscribedTable').DataTable().ajax
                                        .reload(null, false);
                                });
                            },
                            error: function() {
                                Swal.fire("Erreur", "Une erreur s'est produite.",
                                    "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
