@extends('layouts.authenticated.owners.index')

@section('page-title', 'Liste des partenaires')

@section('dashboard-content')
    <div class="container">
        <h1 class="my-4">Gestion des Partenaires</h1>
        <a href="{{ route('partners.create') }}" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Ajouter un partenaire
        </a>

        @if ($partners->isEmpty())
            <p>Aucun partenaire trouvé.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Logo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($partners as $partner)
                        <tr>
                            <td>{{ $partner->name }}</td>
                            <td>
                                @if ($partner->hasMedia('logo'))
                                    <img src="{{ $partner->getFirstMediaUrl('logo', 'thumb') }}" alt="{{ $partner->name }}"
                                        style="max-height: 50px;">
                                @else
                                    <span>Aucun logo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('partners.edit', $partner) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                <form action="{{ route('partners.destroy', $partner) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Voulez-vous vraiment supprimer ce partenaire ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
