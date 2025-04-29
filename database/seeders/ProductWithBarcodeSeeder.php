<?php

namespace Database\Seeders;

use App\Models\Barcode;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Milon\Barcode\DNS1D;

class ProductWithBarcodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $dns1d = new DNS1D();
        $barcodeDir = storage_path('app/public/barcodes');

        if (!file_exists($barcodeDir)) {
            mkdir($barcodeDir, 0755, true);
        }

        for ($i = 1; $i <= 10; $i++) {
            $product = Product::create([
                'name' => 'Product ' . $i,
                'photo' => null,
                'unit' => 'pcs',
                'price_uzs' => rand(10000, 90000),
                'price_usd' => rand(5, 15),
                'tax' => rand(5, 20),
                'short_description' => 'Description for product ' . $i,
                'sale_price' => rand(8000, 88000),
                'category_id' => 1, // make sure these exist in DB
                'brand_id' => 1,
            ]);

            $barcodeValue = str_pad($product->category_id, 2, '0', STR_PAD_LEFT)
                          . str_pad($product->id, 5, '0', STR_PAD_LEFT);

            $barcodeSVG = $dns1d->getBarcodeSVG($barcodeValue, 'C39', 1, 60);
            $barcodePath = 'barcodes/' . $barcodeValue . '.svg';
            file_put_contents(storage_path('app/public/' . $barcodePath), $barcodeSVG);

            Barcode::create([
                'barcode' => $barcodeValue,
                'product_id' => $product->id,
                'barcode_path' => $barcodePath,
            ]);
        }
    }
}
