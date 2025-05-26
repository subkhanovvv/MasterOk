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
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();

        $activities = ProductActivity::whereBetween('created_at', [$startDate, $endDate])->get();

        $netCash = 0;
        $activityTypeCounts = [
            'consume' => 0,
            'loan' => 0,
            'return' => 0,
            'intake' => 0,
            'intake_loan' => 0,
            'intake_return' => 0,
        ];

        foreach ($activities as $activity) {
            $type = $activity->type;
            if (!isset($activityTypeCounts[$type])) {
                continue;
            }

            $activityTypeCounts[$type]++;

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
                    if ($activity->loan_direction === 'given') {
                        $netCash += ($activity->total_price + $activity->loan_amount);
                    } elseif ($activity->loan_direction === 'taken') {
                        $netCash -= $activity->loan_amount;
                    }
                    break;
            }
        }
        $givenLoanTotal = 0;
        $takenLoanTotal = 0;

        $incompleteLoans = ProductActivity::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'incomplete')
            ->whereIn('type', ['loan', 'intake_loan'])
            ->get();

        foreach ($incompleteLoans as $loan) {
            $difference = $loan->total_price - $loan->loan_amount;

            if ($loan->loan_direction === 'given') {
                $givenLoanTotal += $difference;
            } elseif ($loan->loan_direction === 'taken') {
                $takenLoanTotal += $difference;
            }
        }

        return view('pages.report.report', compact(
            'netCash',
            'activityTypeCounts',
            'startDate',
            'endDate',
            // 'softProfit',
            'givenLoanTotal',
            'takenLoanTotal'
        ));
    }
}
