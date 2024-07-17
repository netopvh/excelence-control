<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusType;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
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



        return response()->json([
            'success' => true,
            'data' => [
                'approved' => $approved,
                'waitingApproval' => $waitingApproval,
                'waitingArt' => $waitingArt,
                'itemsToBuy' => $itemsToBuy,
                'lateOrders' => $lateOrders,
                'lateProducts' => $lateProducts
            ]
        ]);
    }
}
