<?php

namespace App\Http\Controllers\Api;

use App\DataTables\OrderDataTableEditor;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function productsOrder(Request $request, $orderId)
    {
        $order = Order::query()->findOrFail($orderId);

        $orderProducts = $order->orderProducts()->with('product')->get();

        return DataTables::of($orderProducts)
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
            ->setRowId(function ($orderProduct) {
                return $orderProduct->id; // Definindo o ID correto do orderProduct
            })
            ->rawColumns(['in_stock', 'supplier'])
            ->make(true);
    }

    public function updateStatusAndStep(Request $request, $id)
    {
        $order = Order::query()->findOrFail($id);
        $order->status = $request->status;
        $order->step = $request->step;
        $order->employee_id = $request->employee_id;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Informações atualizadas com sucesso',
        ]);
    }

    public function updateInfo(Request $request, $id)
    {
        $order = Order::query()->findOrFail($id);
        $order->orderProducts()->find($request->id)
            ->update([
                'supplier' => $request->supplier,
                'link' => $request->link,
                'obs' => $request->obs,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Informações atualizadas com sucesso',
        ]);
    }

    private function checkStringIsLink($string)
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false;
    }
}
