<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActionType;
use App\Enums\OriginType;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $ordersQuery = Order::whereHas('orderProducts', function ($query) use ($request) {
            $query->whereIn('in_stock', ['no', 'partial']);
        })
            ->with(['orderProducts' => function ($query) {
                $query->whereIn('in_stock', ['no', 'partial']);
            }, 'orderProducts.product', 'customer', 'employee'])
            ->orderBy('date', 'desc');

        return DataTables::of($ordersQuery)
            ->filter(function ($query) use ($request) {
                if ($request->get('status') !== 'all') {
                    $query->whereHas('orderProducts', function ($query) use ($request) {
                        $query->where('was_bought', $request->get('status'));
                    });
                }

                if ($request->get('month') !== 'all') {
                    $query->whereHas('orderProducts', function ($query) use ($request) {
                        $query->whereMonth('arrival_date', $request->get('month'));
                    });
                }
            })
            ->editColumn('date', function ($model) {
                return $model->date->format('d/m/Y');
            })
            ->editColumn('delivery_date', function ($model) {
                return $model->delivery_date->format('d/m/Y');
            })
            ->addColumn('action', function ($model) {
                return $model->id;
            })
            ->setRowId('id')
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($id)
    {
        $order = Order::whereHas('orderProducts', function ($query) {
            $query->whereIn('in_stock', ['no', 'partial']);
        })->with(['orderProducts' => function ($query) {
            $query->whereIn('in_stock', ['no', 'partial']);
        }, 'orderProducts.product', 'customer', 'employee'])
            ->findOrFail($id);

        return response()->json($order);
    }

    public function orderItems($id)
    {
        $order = Order::whereHas('orderProducts', function ($query) {
            $query->whereIn('in_stock', ['no', 'partial']);
        })->with(['orderProducts' => function ($query) {
            $query->whereIn('in_stock', ['no', 'partial']);
        }, 'orderProducts.product', 'customer', 'employee'])
            ->findOrFail($id);

        return DataTables::of($order->orderProducts)
            ->editColumn('arrival_date', function ($model) {
                return $model->arrival_date ? Carbon::parse($model->arrival_date)->format('d/m/Y') : null;
            })
            ->setRowId('id')
            ->make(true);
    }

    public function checkUserViewed(Request $request, $id)
    {
        $orders = Order::whereId($id)
            ->whereHas('movements', function ($query) use ($request) {
                $query->where('origin', OriginType::Purchase())
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
            'origin' => OriginType::Purchase(),
            'action_type' => ActionType::Ciencia(),
            'action_user_id' => $request->user_id,
            'action_date' => now(),
        ]);

        return response()->json([
            'message' => 'Registro salvo com sucesso',
        ]);
    }

    public function showProductInfo($id, $productId)
    {
        $order = Order::query()->findOrFail($id);
        $product = $order->orderProducts()->where('id', $productId)->first();

        return response()->json([
            'success' => true,
            'data' => ProductResource::make($product),
        ]);
    }

    public function updateProductInfo(Request $request, $id, $productId)
    {
        $order = Order::query()->findOrFail($id);

        $order->orderProducts()->where('id', $productId)->update([
            'arrived' => $request->arrived,
            'arrival_date' => $request->arrival_date,
            'purchase_date' => $request->purchase_date,
            'was_bought' => $request->was_bought,
        ]);

        return response()->json([
            'message' => 'Registro salvo com sucesso',
        ]);
    }
}
