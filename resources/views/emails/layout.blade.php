<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .email-header {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header img {
            max-width: 140px;
            height: auto;
            margin-bottom: 12px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .email-body {
            padding: 35px 30px;
        }
        .email-body h2 {
            color: #0d6efd;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .email-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 20px 30px;
            text-align: center;
            font-size: 13px;
            color: #6c757d;
        }
        .btn-primary {
            display: inline-block;
            background-color: #0d6efd;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            font-weight: 600;
            border-radius: 6px;
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 50px;
            background-color: #e7f1ff;
            color: #0d6efd;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .table-summary {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table-summary th, .table-summary td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
            text-align: left;
        }
        .table-summary th {
            background-color: #f8f9fa;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <img src="{{ asset('images/LOGO_LADATEMA_SARL.png') }}" alt="{{ config('app.name') }}" onerror="this.style.display='none'">
            <h1>{{ config('app.name', 'Ladatema') }}</h1>
        </div>

        <div class="email-body">
            @yield('content')
        </div>

        <div class="email-footer">
            <p style="margin: 0 0 8px 0;">Besoin d'aide ? Contactez notre équipe sur WhatsApp : <strong>+228 929 808 42</strong></p>
            <p style="margin: 0;">© {{ date('Y') }} {{ config('app.name', 'Ladatema') }}. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
