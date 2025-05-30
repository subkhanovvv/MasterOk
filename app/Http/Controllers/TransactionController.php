<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\ProductActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use App\Models\Setting;
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
        $settings = Setting::find(1);


        // Convert dates to Carbon for proper comparison
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $query = ProductActivity::with(['items.product.brand', 'supplier'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($brandId) {
            $query->whereIn('brand_id', function ($q) use ($brandId) {
                $q->select('id')->from('brands')->where('id', $brandId);
            });
        }
        if ($request->side === 'consume') {
            $query->whereIn('type', ['consume', 'loan', 'return']);
        } elseif ($request->side === 'intake') {
            $query->whereIn('type', ['intake', 'intake_loan', 'intake_return']);
        } elseif ($request->side === 'loan') {
            $query->whereIn('type', ['loan', 'intake_loan']);
        } elseif ($request->side === 'return') {
            $query->whereIn('type', ['return', 'intake_return']);
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
        $loanTotals = [
            'given' => 0,
            'taken' => 0,
        ];

        $softProfit = 0;

        foreach ($activities as $activity) {
            $type = $activity->type;
            $direction = $activity->loan_direction;
            $status = $activity->status;
            $price = $activity->total_price;
            $loan = $activity->loan_amount ?? 0;

            if (array_key_exists($type, $activityTypeCounts)) {
                $activityTypeCounts[$type]++;
            }

            $counts[$type]++;

            if (in_array($type, ['loan', 'intake_loan']) && $status === 'incomplete') {
                if ($direction === 'given') {
                    $loanTotals['given'] += $loan;
                } elseif ($direction === 'taken') {
                    $loanTotals['taken'] += $loan;
                }
            }


            if (in_array($type, ['consume', 'loan']) && $status === 'complete') {
                foreach ($activity->items as $item) {
                    $salePrice = $item->product->sale_price;
                    $costPrice = $item->product->price_uzs ?? 0;
                    $quantity = $item->qty;

                    $softProfit += ($salePrice - $costPrice) * $quantity;
                }
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
            'activityTypeCounts',
            'loanTotals',
            'start',
            'end',
            'brands',
            'brandId',
            'settings',
        ));
    }
    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf'); // default PDF

        $start = $request->input('start_date') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = $request->input('end_date') ?? Carbon::now()->endOfDay()->format('Y-m-d');
        $brandId = $request->input('brand_id');
        $side = $request->input('side');

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $query = ProductActivity::with(['items.product.brand', 'supplier'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($brandId) {
            $query->whereHas('items.product', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            });
        }

        if ($side === 'consume') {
            $query->whereIn('type', ['consume', 'loan', 'return']);
        } elseif ($side === 'intake') {
            $query->whereIn('type', ['intake', 'intake_loan', 'intake_return']);
        } elseif ($side === 'loan') {
            $query->whereIn('type', ['loan', 'intake_loan']);
        } elseif ($side === 'return') {
            $query->whereIn('type', ['return', 'intake_return']);
        }

        $activities = $query->get();

        if ($format === 'excel') {
            return Excel::download(new ReportExport($activities), 'report.xlsx');
        }

        // PDF
        $pdf = FacadePdf::loadView('pages.report.partials.pdf', compact('activities', 'start', 'end', 'side', 'brandId'));
        return $pdf->download('report.pdf');
    }
}
