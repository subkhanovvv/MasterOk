<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Print</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                padding: 0;
                margin: 0;
                font-family: Arial, sans-serif;
            }

            /* Set up the grid to display 5 rows and 4 columns */
            .barcode-container {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                /* 5 columns */
                grid-gap: 5px;
                /* Reduced gap between grid items */
                page-break-before: always;
                margin: 0 auto;
            }

            /* Style for barcode items */
            .barcode-item {
                text-align: center;
                height: 1.7in;
                /* Set fixed height */
                padding-top: 10%;
                border: 1px solid #ddd;
                /* Optional border for clarity */
                box-sizing: border-box;
                /* Prevent overflow due to padding */
                flex-direction: column;
                justify-content: center;
            }


            .barcode-item svg {
                max-width: 100%;
                max-height: 80%;
            }

            .barcode-item p {
                font-size: 12px;
                margin-top: 5px;
                font-weight: bold;
                word-wrap: break-word;
                justify-content: center
            }
        }
    </style>
</head>


<body onload="window.print()" class="mt-3">
    <div class="barcode-container">
        @for ($i = 0; $i < $copies; $i++)
            <div class="barcode-item">
                <div class="my-2"> {!! file_get_contents(storage_path('app/public/' . $product->barcode)) !!} </div>
            </div>
        @endfor
    </div>
</body>


</html>
