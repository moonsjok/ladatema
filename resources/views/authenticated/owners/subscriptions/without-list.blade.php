@extends('layouts.authenticated.owners.index')

@section('page-title', 'Étudiants sans souscription')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('dashboard-content')
    <div class="container">
        <h1>Étudiants sans souscription</h1>
        <table id="students-without-table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Nom étudiant</th>
                    <th>Contacts</th>
                    <th>Dernière connexion</th>
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
            $('#students-without-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('subscriptions.students') }}',
                    data: {
                        type: 'without_subscriptions'
                    }
                },
                columns: [{
                        data: 'nom_complet'
                    },
                    {
                        data: 'contacts'
                    },
                    {
                        data: 'last_login'
                    }
                ]
            });
        });
    </script>
@endpush
