<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActionType;
use App\Enums\MovementType;
use App\Enums\OriginType;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductionController extends Controller
{

    public function index(Request $request)
    {

        $ordersQuery = Order::query()
            ->with('orderProducts.product', 'customer', 'employee');

        return DataTables::of($ordersQuery)
            ->filter(function ($query) use ($request) {
                if ($searchValue = trim($request->get('search')['value'])) {
                    $query->filterBySearch($searchValue);
                }

                $query->when($request->get('step') !== 'all', function ($query) use ($request) {
                    $query->whereHas('orderProducts', function ($query) use ($request) {
                        $query->where('step', $request->get('step'));
                    });
                    $query->with(['orderProducts' => function ($query) use ($request) {
                        $query->where('step', $request->get('step'));
                    }]);
                });
            })
            ->editColumn('date', function (Order $model) {
                return $model->date_formatted;
            })
            ->editColumn('delivery_date', function (Order $model) {
                return $model->delivery_date_formatted;
            })
            ->addColumn('action', function (Order $model) {
                return $model->id;
            })
            ->setRowId('id')
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($id)
    {
        $order = Order::query()
            ->with('orderProducts.product', 'customer', 'employee')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => OrderResource::make($order),
        ]);
    }

    public function updateOrderItem(Request $request, $id, $itemId)
    {
        $this->validate($request, [
            'arrived' => 'required',
            'delivered_date' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        $order = Order::query()->findOrFail($id);
        $orderItem = $order->orderProducts()->findOrFail($itemId);

        $order->movements()->create([
            'action_date' => now(),
            'action_type' => ActionType::Register(),
            'action_user_id' => $request->get('user_id'),
            'movement_type' => MovementType::InProduction(),
            'origin' => OriginType::Production(),
        ]);

        $orderItem->update([
            'arrived' => $request->get('arrived'),
            'delivered_date' => $request->get('delivered_date'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item atualizado com sucesso',
        ]);
    }

    public function checkUserViewed(Request $request, $id)
    {
        $orders = Order::whereId($id)
            ->whereHas('movements', function ($query) use ($request) {
                $query->where('origin', OriginType::Production())
                    ->where('action_user_id', $request->user_id)
                    ->where('action_date', '!=', null);
            })
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'exists' => false,
                'message' => 'Nenhum registro encontrado',
            ]);
        }

        return response()->json([
            'exists' => true,
            'message' => 'Registro encontrado',
        ]);
    }

    public function userViewed(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->movements()->create([
            'origin' => OriginType::Production(),
            'action_type' => ActionType::Ciencia(),
            'action_user_id' => $request->user_id,
            'action_date' => now(),
        ]);

        return response()->json([
            'message' => 'Registro salvo com sucesso',
        ]);
    }
}
