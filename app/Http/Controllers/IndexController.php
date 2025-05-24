<?php

namespace App\Http\Controllers;

use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $day = $request->query('day', 'today');
        $date = $day === 'yesterday' ? Carbon::yesterday() : Carbon::today();

        $todayActivities = ProductActivity::whereDate('created_at', $date)->get();

        $netCash = 0;
        $activityTypeCounts = [
            'consume' => 0,
            'loan' => 0,
            'return' => 0,
            'intake' => 0,
            'intake_loan' => 0,
            'intake_return' => 0,
        ];

        foreach ($todayActivities as $activity) {
            $type = $activity->type;
            $activityTypeCounts[$type] += 1;

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

        // --- Incomplete loan activities due this week ---
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $activities = ProductActivity::where(function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('loan_due_to', [$startOfWeek, $endOfWeek])
                ->orWhere(function ($q) use ($startOfWeek) {
                    $q->where('loan_due_to', '<', $startOfWeek)
                        ->where('status', '!=', 'complete');
                });
        })
            ->where('status', '!=', 'complete')
            ->orderByRaw("CASE WHEN loan_due_to < ? THEN 0 ELSE 1 END", [$startOfWeek]) // overdue first
            ->orderBy('loan_due_to', 'asc')
            ->get();


        // --- Top 5 consumed products in the last 7 days ---
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $topProducts = ProductActivityItems::select('product_id', DB::raw('SUM(qty) as total'))
            ->whereHas('productActivity', function ($q) use ($sevenDaysAgo) {
                $q->where('type', 'consume')->where('created_at', '>=', $sevenDaysAgo);
            })
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->with('product:id,name')
            ->limit(5)
            ->get();

        $labels = $topProducts->pluck('product.name');
        $data = $topProducts->pluck('total');

        return view('pages.index', compact(
            'activities',
            'labels',
            'data',
            'netCash',
            'activityTypeCounts',
            'day'
        ));
    }
}
