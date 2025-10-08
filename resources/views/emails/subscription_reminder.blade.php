<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel de Souscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
        }

        .footer {
            background-color: #f9f9f9;
            text-align: center;
            padding: 10px;
            color: #888;
            font-size: 12px;
            margin-top: 30px;
        }

        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/LOGO_LADATEMA_SARL.png') }}" alt="Logo Ladatema" class="img-fluid mb-2"
                style="max-width: 150px;">
            <h2 class="mt-0">{{ config('app.name') }}</h2>
        </div>

        <div class="content">
            {!! $content !!} <!-- Changement de nom ici -->
        </div>

        <hr />

        <p class="text-center">Bien à vous,</p>
        <p class="text-center">
            <strong>{{ config('app.name') }}</strong><br>
            L’équipe {{ config('app.name') }}
        </p>

        <div class="footer">
            <i class="bi bi-envelope me-1"></i>
            Vous recevez ce message parce que vous êtes inscrit(e) sur {{ config('app.name') }}.<br>
            Si vous ne souhaitez plus recevoir de messages de notre part, veuillez vous désabonner.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>
</body>

</html>
