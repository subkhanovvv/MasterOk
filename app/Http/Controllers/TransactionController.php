<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Supplier;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Validation\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Default to today's date if not specified
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $brandId = $request->input('brand_id');
        $loanDirection = $request->input('loan_direction');
        $type = $request->input('type', 'loan');

        // Base query with proper eager loading
        $query = ProductActivity::with(['items.product.brand'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('type', $type);

        // Apply filters
        if ($brandId) {
            $query->whereHas('items.product', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            });
        }

        if ($loanDirection) {
            $query->where('loan_direction', $loanDirection);
        }

        $activities = $query->get();

        // Prepare data for chart
        $chartData = $this->prepareChartData($activities);

        // Get total profit
        $totalProfit = $activities->sum('total_price');

        // Get brands for filter dropdown
        $brands = Brand::all();

        return view('pages.report.report', compact(
            'activities',
            'chartData',
            'totalProfit',
            'brands',
            'startDate',
            'endDate',
            'brandId',
            'loanDirection',
            'type'
        ));
    }

    private function prepareChartData($activities)
    {
        $productSales = [];

        foreach ($activities as $activity) {
            foreach ($activity->items as $item) {
                if (!isset($item->product)) {
                    continue; // Skip if product relationship not loaded
                }

                $productName = $item->product->name;
                $totalPrice = $item->qty * $item->product->price_uzs;

                if (!isset($productSales[$productName])) {
                    $productSales[$productName] = 0;
                }

                $productSales[$productName] += $totalPrice;
            }
        }

        // Sort by highest selling and take top 5
        arsort($productSales);
        $topProducts = array_slice($productSales, 0, 5, true);

        // Ensure we have at least one product
        if (empty($topProducts)) {
            $topProducts = ['No Data' => 0];
        }

        return [
            'labels' => array_keys($topProducts),
            'data' => array_values($topProducts),
            'colors' => $this->generateChartColors(count($topProducts))
        ];
    }

    private function generateChartColors($count)
    {
        $colors = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(153, 102, 255, 0.7)'
        ];

        return array_slice($colors, 0, $count);
    }

    public function download(Request $request)
    {
        // Implement your download logic here
        // This would generate a CSV or PDF of the report

        return back();
    }
}
