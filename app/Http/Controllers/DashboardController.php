<?php

namespace App\Http\Controllers;

use App\Enums\MovementType;
use App\Enums\StatusType;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $notInSteps = ['finished', 'shipping', 'canceled', 'pickup'];

        $approved = Order::query()
            ->whereHas('orderProducts', function ($query) {
                $query->where('status', StatusType::Approved());
            })
            ->whereNotIn('step', $notInSteps)
            ->count();

        $waitingApproval = Order::query()
            ->whereHas('orderProducts', function ($query) {
                $query->where('status', StatusType::WaitingApproval());
            })
            ->whereNotIn('step', $notInSteps)
            ->count();

        $waitingArt = Order::query()
            ->whereHas('orderProducts', function ($query) {
                $query->where('status', StatusType::WaitingDesign());
            })
            ->whereNotIn('step', $notInSteps)
            ->count();

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

        $orderItems = Order::query()
            ->with(['orderProducts' => function ($query) {
                $query->whereIn('in_stock', ['no', 'partial'])
                    ->where('was_bought', 'N');
            }])
            ->whereHas('orderProducts', function ($query) {
                $query->whereIn('in_stock', ['no', 'partial'])
                    ->where('was_bought', 'N');
            })
            ->get();

        $itemsToBuy = $orderItems->reduce(function ($carry, $order) {
            return $carry + $order->orderProducts->count();
        }, 0);

        $lateOrders = Order::query()
            ->whereDate('delivery_date', Carbon::tomorrow()->format('Y-m-d'))->count();

        $lateProducts = Order::query()
            ->with(['orderProducts' => function ($query) {
                $query->whereIn('in_stock', ['no', 'partial'])
                    ->where('was_bought', 'Y')
                    ->whereDate('arrival_date', Carbon::tomorrow()->format('Y-m-d'));
            }])
            ->whereHas('orderProducts', function ($query) {
                $query->whereIn('in_stock', ['no', 'partial'])
                    ->where('was_bought', 'Y')
                    ->whereDate('arrival_date', Carbon::tomorrow()->format('Y-m-d'));
            })
            ->get()
            ->reduce(function ($carry, $order) {
                return $carry + $order->orderProducts->count();
            }, 0);

        return view('pages.dashboard', compact('approved', 'waitingApproval', 'waitingArt', 'stepInDesign', 'stepInProduction', 'stepFinished', 'stepShipped', 'stepPickup', 'itemsToBuy', 'lateOrders', 'lateProducts'));
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
