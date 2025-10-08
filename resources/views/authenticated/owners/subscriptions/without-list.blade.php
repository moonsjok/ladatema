@extends('layouts.authenticated.owners.index')

@section('page-title', 'Étudiants sans souscription')

@section('dashboard-content')
    <div class="container">
        <h1>Étudiants sans souscription</h1>
        <table id="students-without-table" class="table table-striped">
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
