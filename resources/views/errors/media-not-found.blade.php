<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichier non trouvé</title>
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
                            <i class="bi bi-file-earmark-x text-info" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h3 class="card-title text-info mb-3">Fichier non trouvé</h3>
                        
                        <p class="card-text text-muted mb-4">
                            {{ $message ?? 'Le fichier demandé n\'existe pas ou a été supprimé.' }}
                        </p>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-house"></i> Retour à l'accueil
                            </a>
                            <a href="{{ route('media.index', 'images') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-images"></i> Voir les médias
                            </a>
                        </div>
                        
                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Conseil:</strong> 
                            Vérifiez que l'URL est correcte ou que le fichier n'a pas été déplacé.
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Si le problème persiste, 
                        <a href="{{ route('dashboard') }}">contactez l'administrateur</a>.
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
