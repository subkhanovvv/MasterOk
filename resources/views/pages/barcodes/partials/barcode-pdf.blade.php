<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode - {{ $product->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10mm;
        }
        .barcode-container {
            display: grid;
            grid-template-columns: repeat({{ $perRow }}, 1fr);
            gap: 5mm;
        }
        .barcode-item {
            text-align: center;
            border: 0.5pt dashed #eee;
            padding: 3mm;
            page-break-inside: avoid;
        }
        .product-name {
            font-size: 9pt;
            margin-bottom: 2mm;
            word-break: break-word;
        }
        .barcode-image {
            width: 100%;
            height: 25mm;
            margin: 2mm 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode-image img {
            max-width: 100%;
            max-height: 100%;
        }
        .barcode-value {
            font-size: 8pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="barcode-container">
        @for($i = 0; $i < $copies; $i++)
            <div class="barcode-item">
                <div class="product-name">{{ $product->name }}</div>
                <div class="barcode-image">
                    <img src="{{ $barcodeImage }}" alt="Barcode">
                </div>
                <div class="barcode-value">{{ $product->barcode_value }}</div>
            </div>
        @endfor
    </div>
</body>
</html>