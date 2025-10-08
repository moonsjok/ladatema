{{-- // resources/views/categories/create.blade.php --}}
@extends('layouts.authenticated.owners.index')

@section('dashboard-content')
    <h1>Créer une catégorie</h1>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <div>
            <label for="name">Nom de la catégorie</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nom de la catégorie">
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" placeholder="Description de la catégorie">{{ old('description') }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">Créer</button>
    </form>
@endsection
