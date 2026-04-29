@extends('layouts.authenticated.owners.index')
@section('page-title', 'Les tentatives')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/evaluation-creation.css') }}">
@endpush

@section('dashboard-content')
    <div class="container">
        <div class="row">
            @foreach ($attempts as $key => $attempt )
                <div class="col-md-4">
                    <div class="card  p-3 m-2"> 
                        {{$key}}
                        {{$attempt->evaluation->title}}
                    </div> 
                </div> 
            @endforeach
        </div>
        <div class="row mt-4">
            <div class="col-12  d-flex justify-content-center">
            {{-- Afficher les liens de pagination --}}
                {{ $attempts->links() }}
            </div>
        </div>
    </div>
@endsection