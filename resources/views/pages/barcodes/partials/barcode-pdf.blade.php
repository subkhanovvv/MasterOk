<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Barcode Sheet</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .barcode-sheet {
            width: 100%;
            text-align: center;
            font-size: 0;
        }

        .barcode-cell {
            display: inline-block;
            width: 15%;
            /* 5 items per row with small spacing */
            height: 3cm;
            margin: 1.1%;
            border: 1px solid #ccc;
            padding: 5px;
            box-sizing: border-box;
            text-align: center;
            vertical-align: top;
        }

        .barcode-img {
            width: 80%;
            height:1.2cm;
            object-fit: contain;
            /* margin-bottom: 5px; */
        }

        .product-name {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .barcode-number {
            font-size: 8pt;
            font-family: 'Courier New', monospace;
        }
    </style>

</head>

<body>
    @php
        $barcodesPerPage = 25; // 5 columns x 5 rows
        $totalPages = ceil($copies / $barcodesPerPage);
    @endphp

    @for ($page = 1; $page <= $totalPages; $page++)
        <div class="barcode-sheet">
            @for ($i = 0; $i < $copies; $i++)
                <div class="barcode-cell">
                     {!! $barcodeSvg !!}
                    <div class="product-name">{{ Str::limit($product->name, 12) }}</div>
                    {{-- <div class="barcode-number">{{ $product->barcode }}</div> --}}
                </div>
            @endfor
        </div>

    @endfor
</body>

</html>
