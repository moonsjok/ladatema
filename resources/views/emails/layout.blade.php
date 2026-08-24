<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            color: #2b2f32;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f7f6;
            padding: 30px 15px;
            box-sizing: border-box;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            border: 1px solid #eaedf0;
        }
        .email-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #ffffff;
            padding: 32px 25px;
            text-align: center;
        }
        .email-header img {
            max-width: 150px;
            height: auto;
            margin-bottom: 12px;
            display: inline-block;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        .email-body {
            padding: 35px 30px;
            color: #333333;
        }
        .email-body h2 {
            color: #0d6efd;
            font-size: 20px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 18px;
        }
        .email-body p {
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 15px;
            color: #495057;
            line-height: 1.6;
        }
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 13px 32px;
            font-weight: 600;
            font-size: 15px;
            border-radius: 50px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }
        .btn-primary:hover {
            background: #0b5ed7;
        }
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #0d6efd;
            padding: 18px 20px;
            margin: 22px 0;
            border-radius: 8px;
        }
        .warning-box {
            background-color: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 18px 20px;
            margin: 22px 0;
            border-radius: 8px;
        }
        .danger-box {
            background-color: #fff5f5;
            border-left: 4px solid #dc3545;
            padding: 18px 20px;
            margin: 22px 0;
            border-radius: 8px;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 50px;
            background-color: #e7f1ff;
            color: #0d6efd;
        }
        .table-summary {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
        .table-summary th, .table-summary td {
            padding: 12px 16px;
            border-bottom: 1px solid #e9ecef;
            text-align: left;
            font-size: 14px;
        }
        .table-summary th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
        }
        .email-footer {
            background-color: #f8fafc;
            border-top: 1px solid #edf2f7;
            padding: 22px 30px;
            text-align: center;
            font-size: 13px;
            color: #718096;
        }
        .email-footer a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
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
    </div>
</body>
</html>
