@extends('layouts.authenticated.owners.index')

@section('page-title', "Vue d'ensemble des souscriptions")

@section('dashboard-content')
    <div class="container">
        <h1>Vue d'ensemble des souscriptions</h1>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Total d'utilisateurs sans souscription</h5>
                    <p class="display-6">{{ $countWithout }}</p>
                    <a href="{{ route('subscriptions.students.without') }}" class="btn btn-primary">Voir la liste</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Total d'utilisateurs avec souscription</h5>
                    <p class="display-6">{{ $countWith }}</p>
                    <small class="text-muted">Total de souscriptions (toutes formations) : <strong>{{ $totalSubscriptions ?? 0 }}</strong></small>
                    <a href="{{ route('subscriptions.students.with') }}" class="btn btn-primary">Voir la liste</a>
                </div>
            </div>
        </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5>Utilisateurs ayant plus d'une souscription</h5>
                        <p class="display-6">{{ $countMultiple ?? 0 }}</p>
                    </div>
                </div>
            </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card p-3">
                    <h5>Total de souscriptions par formation</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Formation</th>
                                <th>Total de souscriptions</th>
                                <th>Utilisateurs uniques</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subsPerFormation as $f)
                                <tr>
                                    <td>{{ $f['formation_title'] ?? ($f['title'] ?? '—') }}</td>
                                    {{-- support older key 'total' if present; prefer explicit 'total_subscriptions' from controller --}}
                                    <td>{{ $f['total_subscriptions'] ?? $f['total'] ?? 0 }}</td>
                                    <td>{{ $f['unique_users'] ?? $f['unique'] ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">Aucune souscription par formation pour le moment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
