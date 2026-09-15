@extends('layouts.authenticated.owners.index')

@section('page-title', 'Gestion des Utilisateurs')

@section('dashboard-content')
    <div class="container-fluid py-3">
        @livewire('authenticated.user-manager')
    </div>
@endsection
