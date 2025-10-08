@extends('layouts.authenticated.owners.index')

@section('page-title', 'Modifier le Partenaire')

@section('dashboard-content')
    <div class="container">
        <h1 class="my-4">Modifier le Partenaire</h1>
        <form action="{{ route('partners.update', $partner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nom du partenaire</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $partner->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Logo (laisser vide pour conserver l’existant)</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                @if ($partner->hasMedia('logo'))
                    <img src="{{ $partner->getFirstMediaUrl('logo', 'thumb') }}" alt="{{ $partner->name }}" class="mt-2"
                        style="max-height: 50px;">
                @endif
                @error('logo')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Mettre à jour
            </button>
            <a href="{{ route('partners.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </form>
    </div>
@endsection
