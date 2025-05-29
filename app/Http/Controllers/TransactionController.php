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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use PDF;

class TransactionController extends Controller
{
   public function index(Request $request)
{
    // Set default dates (start of month to today)
    $start = $request->input('start_date') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
    $end = $request->input('end_date') ?? Carbon::now()->endOfDay()->format('Y-m-d');
    $brandId = $request->input('brand_id');

    // Convert dates to Carbon for proper comparison
    $startDate = Carbon::parse($start)->startOfDay();
    $endDate = Carbon::parse($end)->endOfDay();

    $query = ProductActivity::with(['items.product.brand', 'supplier'])
        ->whereBetween('created_at', [$startDate, $endDate]);

    if ($brandId) {
        $query->whereHas('items.product', function ($q) use ($brandId) {
            $q->where('brand_id', $brandId);
        });
    }
    if ($request->side === 'consume') {
        $query->whereIn('type', ['consume', 'loan', 'return']);
    } elseif ($request->side === 'intake') {
        $query->whereIn('type', ['intake', 'intake_loan', 'intake_return']);
    }

    $activities = $query->get();

    // Initialize counters
    $activityTypeCounts = [
        'consume' => 0,
        'loan' => 0,
        'return' => 0,
        'intake' => 0,
        'intake_loan' => 0,
        'intake_return' => 0,
    ];

    $counts = [
        'consume' => 0,
        'loan' => 0,
        'return' => 0,
        'intake' => 0,
        'intake_loan' => 0,
        'intake_return' => 0,
    ];

    $netCash = 0;
    $softProfit = 0;
    $softProfitUsd = 0;
    $loanTotals = [
        'given' => 0,
        'taken' => 0,
    ];

    foreach ($activities as $activity) {
        $type = $activity->type;
        $direction = $activity->loan_direction;
        $status = $activity->status;
        $price = $activity->total_price;
        $priceusd = $activity->total_usd ?? $price; // Use USD price if available
        $loan = $activity->loan_amount ?? 0;

        // Update activity type counts
        if (array_key_exists($type, $activityTypeCounts)) {
            $activityTypeCounts[$type]++;
        }
        
        $counts[$type]++;

        // Rest of your existing calculations...
        if (in_array($type, ['consume', 'loan', 'intake_return'])) {
            $softProfit += $price;
        } elseif (in_array($type, ['return', 'intake', 'intake_loan'])) {
            $softProfit -= $price;
        }

        if (in_array($type, ['return', 'intake', 'intake_loan'])) {
            $softProfitUsd -= $priceusd;
        }

        switch ($type) {
            case 'consume':
            case 'intake_return':
                $netCash += $activity->total_price;
                break;

            case 'return':
            case 'intake':
                $netCash -= $activity->total_price;
                break;

            case 'loan':
                if ($activity->status === 'incomplete') {
                    if ($activity->loan_direction === 'given') {
                        $netCash += ($activity->total_price - $activity->loan_amount);
                    } elseif ($activity->loan_direction === 'taken') {
                        $netCash += ($activity->total_price + $activity->loan_amount);
                    }
                } else {
                    $netCash += $activity->total_price;
                }
                break;

            case 'intake_loan':
                if ($activity->status === 'incomplete') {
                    if ($activity->loan_direction === 'given') {
                        $netCash -= ($activity->total_price + $activity->loan_amount);
                    } elseif ($activity->loan_direction === 'taken') {
                        $netCash -= ($activity->total_price - $activity->loan_amount);
                    }
                } else {
                    $netCash -= $activity->total_price;
                }
                break;
        }
    }

    $brands = Brand::all();

    return view('pages.report.report', compact(
        'activities',
        'counts',
        'netCash',
        'softProfit',
        'softProfitUsd',
        'activityTypeCounts',
        'loanTotals',
        'start',
        'end',
        'brands',
        'brandId'
    ));
}
    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf'); // default PDF

        $start = $request->input('start_date') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = $request->input('end_date') ?? Carbon::now()->endOfDay()->format('Y-m-d');
        $brandId = $request->input('brand_id');

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $query = ProductActivity::with(['items.product.brand', 'supplier'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($brandId) {
            $query->whereHas('items.product', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            });
        }

        $activities = $query->get();

        if ($format === 'excel') {
            return Excel::download(new ReportExport($activities), 'report.xlsx');
        }

        // PDF
        $pdf = FacadePdf::loadView('pages.report.partials.pdf', compact('activities', 'start', 'end'));
        return $pdf->download('report.pdf');
    }
}
