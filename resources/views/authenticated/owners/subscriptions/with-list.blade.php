@extends('layouts.authenticated.owners.index')

@section('page-title', 'Étudiants avec souscription')

@section('dashboard-content')
    <div class="container">
        <h1>Étudiants avec souscription</h1>
        <table id="students-with-table" class="table table-striped">
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
                if (!confirm('Valider cette souscription ?')) return;
                $.ajax({
                    url: '/subscriptions/' + id + '/validate',
                    method: 'PUT',
                    success: function() {
                        $('#students-with-table').DataTable().ajax.reload();
                    },
                    error: function() {
                        alert('Erreur lors de la validation');
                    }
                });
            });
        });
    </script>
@endpush
