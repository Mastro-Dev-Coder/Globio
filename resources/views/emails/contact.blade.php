<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo messaggio di contatto - Globio</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 20px;
            margin: -20px -20px 30px -20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-box {
            background-color: #f1f5f9;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .field {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .field:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            display: block;
        }
        .value {
            color: #6b7280;
            word-wrap: break-word;
        }
        .message-content {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 15px;
            margin-top: 5px;
            white-space: pre-wrap;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Nuovo Messaggio di Contatto</h1>
        </div>

        <div class="info-box">
            <strong>Nuovo messaggio ricevuto dal form di contatto di Globio</strong><br>
            <small>Inviato il: {{ $submittedAt }}</small>
        </div>

        <div class="field">
            <span class="label">👤 Nome:</span>
            <div class="value">{{ $nome }}</div>
        </div>

        <div class="field">
            <span class="label">📧 Email:</span>
            <div class="value">
                <a href="mailto:{{ $email }}" style="color: #ef4444; text-decoration: none;">{{ $email }}</a>
            </div>
        </div>

        <div class="field">
            <span class="label">📝 Oggetto:</span>
            <div class="value">{{ $oggetto }}</div>
        </div>

        <div class="field">
            <span class="label">💬 Messaggio:</span>
            <div class="message-content">{{ $messaggio }}</div>
        </div>

        <div class="footer">
            <p><strong>Globio</strong> - La tua piattaforma video preferita</p>
            <p><small>Questo è un messaggio automatico generato dal sistema di contatti di Globio.</small></p>
        </div>
    </div>
</body>
</html>