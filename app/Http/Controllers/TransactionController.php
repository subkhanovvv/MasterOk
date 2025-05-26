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

        $activities = $query->get();

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
        $loanTotals = [
            'given' => 0,
            'taken' => 0,
        ];

        foreach ($activities as $activity) {
            $type = $activity->type;
            $direction = $activity->loan_direction;
            $status = $activity->status;
            $price = $activity->total_price;
            $loan = $activity->loan_amount ?? 0;

            $counts[$type]++;

            // Soft profit calculation (total revenue)
            if (in_array($type, ['consume', 'loan', 'intake_return'])) {
                $softProfit += $price;
            } elseif (in_array($type, ['return', 'intake', 'intake_loan'])) {
                $softProfit -= $price;
            }

            // Net cash calculation (actual cash flow)
            switch ($type) {
                case 'consume':
                case 'intake_return':
                    $netCash += $price;
                    break;

                case 'return':
                case 'intake':
                    $netCash -= $price;
                    break;

                case 'loan':
                    if ($status === 'incomplete') {
                        if ($direction === 'given') {
                            // When giving loan: customer pays (price - loan) now, owes us 'loan' amount
                            $netCash += ($price - $loan);
                            $loanTotals['given'] += $loan;
                        } elseif ($direction === 'taken') {
                            // When taking loan: we pay (price + loan) now, supplier owes us 'loan' amount
                            $netCash -= ($price + $loan);
                            $loanTotals['taken'] += $loan;
                        }
                    } else {
                        // Completed loans are treated as normal transactions
                        $netCash += $price;
                    }
                    break;

                case 'intake_loan':
                    if ($status === 'incomplete') {
                        if ($direction === 'given') {
                            // When giving loan during intake: we pay full price now, customer owes us 'loan' amount
                            $netCash -= $price;
                            $loanTotals['given'] += $loan;
                        } elseif ($direction === 'taken') {
                            // When taking loan during intake: we receive products, owe supplier 'loan' amount
                            $loanTotals['taken'] += $loan;
                        }
                    } else {
                        // Completed intake loans
                        if ($direction === 'given') {
                            $netCash -= ($price - $loan);
                        } elseif ($direction === 'taken') {
                            $netCash += $loan;
                        }
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
            'loanTotals',
            'start',
            'end',
            'brands',
            'brandId'
        ));
    }
}
