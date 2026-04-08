@extends('layouts.authenticated.owners.index')

@section('page-title', 'Étudiants avec souscription')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('dashboard-content')
    <div class="container">
        <h1>Étudiants avec souscription</h1>
        <table id="students-with-table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Référence paiement</th>
                    <th>Nom étudiant</th>
                    <th>Formation</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function() {
            $('#students-with-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('subscriptions.students') }}',
                    data: {
                        type: 'with_subscriptions'
                    }
                },
                columns: [{
                        data: 'payment_reference'
                    },
                    {
                        data: 'student'
                    },
                    {
                        data: 'formation_title'
                    },
                    {
                        data: 'status'
                    }
                ],
                columnDefs: [{
                    targets: [1, 3],
                    searchable: true,
                    orderable: true
                }]
            });

            // handle validate button click
            $(document).on('click', '.validate-subscription', function() {
                const id = $(this).data('id');
                
                Swal.fire({
                    title: 'Valider la souscription',
                    text: 'Êtes-vous sûr de vouloir valider cette souscription ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, valider',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/subscriptions/' + id + '/validate',
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                $('#students-with-table').DataTable().ajax.reload();
                                Swal.fire({
                                    title: 'Succès',
                                    text: response.message || 'Souscription validée avec succès',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Erreur',
                                    text: 'Erreur lors de la validation: ' + (xhr.responseJSON?.message || 'Erreur inconnue'),
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
