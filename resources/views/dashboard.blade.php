@extends('layouts.authenticated.owners.index')
@section('page-title', 'Tableau de bord')
@section('dashboard-content')
    Dashboard

    <a href="{{ route('formation.quickCreate') }}"> AJouter une formation </a>
@endsection
