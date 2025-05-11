<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Print All Barcodes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                margin: 0;
                font-family: Arial, sans-serif;
            }

            .barcode-container {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-gap: 10px;
                margin: 0 auto;
                page-break-inside: avoid;
            }

            .barcode-item {
                text-align: center;
                height: 1.7in;
                padding-top: 10%;
                border: 1px solid #ccc;
                box-sizing: border-box;
                justify-content: center;
                align-items: center;
                page-break-inside: avoid;
            }

            .barcode-item svg {
                max-width: 100%;
                height: auto;
            }

            .barcode-item p {
                font-size: 12px;
                margin-top: 5px;
                font-weight: bold;
                word-wrap: break-word;
            }

            @page {
                size: auto;
                margin: 5mm;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="barcode-container">
        @foreach ($products as $product)
            @for ($i = 0; $i < $copies; $i++)
                <div class="barcode-item">
                    <div class="my-2">
                        {!! file_get_contents(storage_path('app/public/' . $product->barcode)) !!}
                    </div>
                    <p>{{ $product->name }}</p>
                </div>
            @endfor
        @endforeach
    </div>
</body>

</html>
