<?php

namespace App\Http\Controllers;

use App\Enums\MovementType;
use App\Enums\StatusType;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {

        $stepInDesign = Order::query()
            ->where('step', MovementType::InDesign())
            ->count();

        $stepInProduction = Order::query()
            ->where('step', MovementType::InProduction())
            ->count();

        $stepFinished = Order::query()
            ->where('step', MovementType::Finished())
            ->count();

        $stepShipped = Order::query()
            ->where('step', MovementType::Shipping())
            ->count();

        $stepPickup = Order::query()
            ->where('step', MovementType::Pickup())
            ->count();

        return view('pages.dashboard', compact('stepInDesign', 'stepInProduction', 'stepFinished', 'stepShipped', 'stepPickup'));
    }

    public function chartJson(Request $request)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $orders = Order::selectRaw("DATE_FORMAT(date, '%d/%m/%Y') as order_date_formatted, COUNT(*) as total_orders")
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('order_date_formatted')
            ->orderBy('order_date_formatted', 'asc') // Ordena pela data formatada
            ->get();

        return response()->json($orders);
    }
}
