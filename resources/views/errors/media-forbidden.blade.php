<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-shield-exclamation text-warning" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h3 class="card-title text-warning mb-3">Accès refusé</h3>
                        
                        <p class="card-text text-muted mb-4">
                            {{ $message ?? 'Ce fichier est privé ou ne vous appartient pas.' }}
                        </p>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-house"></i> Retour à l'accueil
                            </a>
                            <a href="{{ route('media.index', 'images') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-images"></i> Voir mes médias
                            </a>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle"></i>
                            <strong>Information:</strong> 
                            Seuls les fichiers publics et vos propres fichiers sont accessibles.
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Si vous pensez qu'il s'agit d'une erreur, 
                        <a href="{{ route('dashboard') }}">contactez l'administrateur</a>.
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
