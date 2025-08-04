<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carta de Renuncia</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            margin: 2cm;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            text-align: justify;
        }
        .signature {
            margin-top: 80px;
            text-align: center;
        }
        .footer {
            margin-top: 60px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Carta de Renuncia</h1>

    <p><strong>Nombre del empleado:</strong> {{ $form->full_name }}</p>
    <p><strong>Fecha de renuncia:</strong> {{ \Carbon\Carbon::parse($form->resignation_date)->format('d/m/Y') }}</p>

    <div class="content">
        <p>
            Por medio de la presente, yo <strong>{{ $form->full_name }}</strong>, presento mi renuncia voluntaria a la empresa a partir del día
            <strong>{{ \Carbon\Carbon::parse($form->resignation_date)->translatedFormat('d \d\e F \d\e Y') }}</strong>.
        </p>

        <p>
            El motivo de mi renuncia es el siguiente: <br>
            {{ $form->reason }}
        </p>

        <p>
            Agradezco la oportunidad brindada durante el tiempo que formé parte del equipo y me despido cordialmente.
        </p>
    </div>

    <div class="signature">
        <p>______________________________</p>
        <p>{{ $form->full_name }}</p>
        <p>Firma</p>
    </div>

    <div class="footer">
        Documento generado automáticamente - {{ config('app.name') }}
    </div>
</body>
</html>