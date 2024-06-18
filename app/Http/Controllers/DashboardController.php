<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $approved = Order::query()->where('status', 'aprovado')->count();
        $waitingApproval = Order::query()->where('status', 'aguard. aprov')->count();
        $waitingArt = Order::query()->where('status', 'aguard. arte')->count();

        return view('pages.dashboard', compact('approved', 'waitingApproval', 'waitingArt'));
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
