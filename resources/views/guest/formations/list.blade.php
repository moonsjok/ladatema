@extends('layouts.guest.index')

@section('page-title', 'Liste des formations') <!-- Titre de la page -->

@section('content')
    @php
        $selectedCategory = request()->query('selectedCategory');
    @endphp

    @livewire('guest.formation-list', ['selectedCategory' => $selectedCategory])

@endsection
