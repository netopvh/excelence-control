<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActionType;
use App\Enums\OriginType;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function index()
    {
        $orders = Order::whereHas('orderProducts', function ($query) {
            $query->where('in_stock', 'no');
        })
            ->with(['orderProducts' => function ($query) {
                $query->where('in_stock', 'no');
            }, 'orderProducts.product', 'customer', 'employee'])
            ->orderBy('date', 'desc')
            ->get();

        return DataTables::of($orders)
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
            $query->where('in_stock', 'no');
        })->with(['orderProducts' => function ($query) {
            $query->where('in_stock', 'no');
        }, 'orderProducts.product', 'customer', 'employee'])
            ->findOrFail($id);

        return response()->json($order);
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
}
