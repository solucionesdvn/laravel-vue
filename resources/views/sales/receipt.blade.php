<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Venta</title>
    <style>
        body {
            font-family: 'monospace', sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .receipt {
            width: 80mm;
            margin: 20px auto;
            padding: 15px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-size: 15px; /* Base font size Increased */
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header img {
            max-width: 100%;
            max-height: 60px;
        }
        .header h1 {
            font-size: 1.3em;
            margin: 5px 0;
        }
        .header p {
            font-size: 0.9em;
            margin: 2px 0;
        }
        .info, .items, .totals, .footer {
            margin-bottom: 10px;
        }
        .info p, .footer p {
            font-size: 0.9em;
            margin: 3px 0;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        .items th, .items td {
            padding: 5px;
            border-bottom: 1px dashed #000;
        }
        .items thead th {
            border-bottom: 1px dashed #000; /* Ensure header has a line */
        }
        .items th {
            text-align: left;
        }
        .items .text-right {
            text-align: right;
        }
        .totals table {
            width: 100%;
            font-size: 1em;
        }
        .totals td {
            padding: 3px 4px;
        }
        .totals .total {
            font-weight: bold;
            font-size: 1.2em;
        }
        .totals .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .print-button {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            font-size: 1em;
            cursor: pointer;
            margin-top: 10px;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 3mm 3mm 3mm 5mm; /* top, right, bottom, left */
            }
            body, html {
                width: 80mm;
                height: auto;
                margin: 0 !important;
                padding: 0 !important;
                background-color: #fff;
            }
            .receipt {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
                font-size: 12pt; /* Increased font size for print */
            }
            .print-button {
                display: none;
            }
            /* Additions to prevent header repeating */
            .items thead {
                display: table-row-group;
            }
            .items tbody tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            @if($sale->company->logo)
                <img src="{{ asset('storage/' . $sale->company->logo) }}" alt="Logo">
            @endif
            <h1>{{ $sale->company->name }}</h1>
            <p>NIT: {{ $sale->company->nit ?? 'N/A' }}</p>
            <p>{{ $sale->company->address }}</p>
            <p>Tel: {{ $sale->company->phone }}</p>
        </div>

        <div class="separator"></div>

        <div class="info">
            
            <p>Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
            <p>Cajero: {{ $sale->user->name }}</p>
            <p>Cliente: {{ $sale->client->name ?? 'Consumidor Final' }}</p>
            <p>Método de Pago: {{ $sale->paymentMethod->name ?? 'N/A' }}</p>
        </div>

        <div class="separator"></div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Producto</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                        <tr>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                {{ $item->product->name }}
                                <br>
                                ({{ number_format($item->unit_price, 2) }})
                            </td>
                            <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="separator"></div>

        <div class="totals">
            <table>
                <tr>
                    <td>TOTAL:</td>
                    <td class="text-right total">$ {{ number_format($sale->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="separator"></div>

        <div class="footer">
            <p>¡Gracias por su compra!</p>
        </div>

        <button class="print-button" onclick="window.print()">Imprimir</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
