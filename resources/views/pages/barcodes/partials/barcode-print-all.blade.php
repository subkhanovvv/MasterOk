<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Print All Barcodes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styles for both screen and print */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
        }

        .barcode-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 0 auto;
        }

        .barcode-item {
            text-align: center;
            height: 1.7in;
            padding: 10px;
            border: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .barcode-item svg,
        .barcode-item img {
            max-width: 100%;
            height: auto;
            margin-bottom: 5px;
        }

        .barcode-item p {
            font-size: 12px;
            margin: 5px 0 0;
            font-weight: bold;
            word-break: break-word;
        }

        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .barcode-container {
                page-break-inside: avoid;
            }

            .barcode-item {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            @page {
                size: auto;
                margin: 5mm;
            }
        }
    </style>
</head>

<body>
    <div class="barcode-container">
        @foreach ($products as $product)
            @for ($i = 0; $i < $copies; $i++)
                <div class="barcode-item">
                    @if (file_exists(storage_path('app/public/' . $product->barcode)))
                        <div class="barcode-image">
                            {!! file_get_contents(storage_path('app/public/' . $product->barcode)) !!}
                        </div>
                    @else
                        <div class="text-danger">Barcode image missing</div>
                    @endif
                    <p>{{ $product->name }}</p>
                </div>
            @endfor
        @endforeach
    </div>

    
</body>

</html>
