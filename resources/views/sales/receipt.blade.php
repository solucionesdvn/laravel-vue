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
            padding: 10px;
            background-color: #fff;
        }
        .receipt {
            width: 80mm;
            margin: 0 auto;
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
            font-size: 1.2em;
            margin: 5px 0;
        }
        .header p {
            font-size: 0.8em;
            margin: 2px 0;
        }
        .info, .items, .totals, .footer {
            margin-bottom: 10px;
        }
        .info p, .footer p {
            font-size: 0.8em;
            margin: 2px 0;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8em;
        }
        .items th, .items td {
            padding: 4px;
            border-bottom: 1px dashed #000;
        }
        .items th {
            text-align: left;
        }
        .items .text-right {
            text-align: right;
        }
        .totals table {
            width: 100%;
            font-size: 0.9em;
        }
        .totals td {
            padding: 2px 4px;
        }
        .totals .total {
            font-weight: bold;
            font-size: 1.1em;
        }
        .totals .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
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
            body {
                margin: 0;
                padding: 0;
            }
            .receipt {
                margin: 0;
                width: 100%;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            @if($sale->company->logo)
                <!-- Assuming logo is a public path -->
                <img src="{{ asset('storage/' . $sale->company->logo) }}" alt="Logo">
            @endif
            <h1>{{ $sale->company->name }}</h1>
            <p>{{ $sale->company->address }}</p>
            <p>Tel: {{ $sale->company->phone }}</p>
            <p>{{ $sale->company->email }}</p>
        </div>

        <div class="info">
            <p>--------------------------------</p>
            <p>Comprobante: #{{ $sale->id }}</p>
            <p>Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
            <p>Cajero: {{ $sale->user->name }}</p>
            <p>Cliente: {{ $sale->client->name ?? 'Consumidor Final' }}</p>
            <p>--------------------------------</p>
        </div>

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

        <div class="totals">
            <p>--------------------------------</p>
            <table>
                <tr>
                    <td>TOTAL:</td>
                    <td class="text-right total">$ {{ number_format($sale->total, 2) }}</td>
                </tr>
            </table>
            <p>--------------------------------</p>
        </div>

        <div class="footer">
            <p>¡Gracias por su compra!</p>
        </div>

        <button class="print-button" onclick="window.print()">Imprimir</button>
    </div>

    <script>
        // Optional: Automatically trigger print dialog on load
        window.onload = function() {
            window.print();
        };

        // Optional: Close window after printing
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
