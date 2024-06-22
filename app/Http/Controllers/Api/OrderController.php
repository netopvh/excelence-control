<?php

namespace App\Http\Controllers\Api;

use App\DataTables\OrderDataTableEditor;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function productsOrder(Request $request, $orderId)
    {
        $order = Order::query()->findOrFail($orderId);

        return DataTables::of($order->orderProducts()->with('product'))
            ->editColumn('in_stock', function ($orderProduct) {
                if ($orderProduct->in_stock === 'yes') {
                    return '<span class="badge bg-success">Sim</span>';
                } else if ($orderProduct->in_stock === 'no') {
                    return '<span class="badge bg-warning">Não</span>';
                } else if ($orderProduct->in_stock === 'partial') {
                    return '<span class="badge bg-info">Parcial</span>';
                } else {
                    return '-';
                }
            })
            ->rawColumns(['in_stock'])
            ->setRowId('id')
            ->make(true);
    }

    public function updateStatusAndStep(Request $request)
    {
        $order = Order::query()->findOrFail($request->order_id);
        $order->status = $request->status;
        $order->step = $request->step;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Informações atualizadas com sucesso',
        ]);
    }
}
