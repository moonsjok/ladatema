@extends('layouts.authenticated.owners.index')

@section('page-title', 'Liste des formations')

@section('dashboard-content')
    @livewire('formation-manager')
@endsection
