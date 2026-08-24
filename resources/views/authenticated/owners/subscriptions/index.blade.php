@extends('layouts.authenticated.owners.index')

@section('page-title', "Gestion des souscriptions")

@section('dashboard-content')
    <div class="container-fluid py-3">
        @livewire('authenticated.subscription-manager')
    </div>
@endsection
