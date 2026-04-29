@extends('layouts.authenticated.students.index')
@section('page-title', "Passer l'évaluation")

@section('dashboard-content')
    <div class="container my-6">
        <livewire:evaluation-take 
            :evaluation="$evaluation" 
            :attempt="$attempt" />
    </div>
@endsection
