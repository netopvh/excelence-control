<?php

namespace App\Http\Controllers\Api;

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
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($orders)
            ->editColumn('date', function ($model) {
                return $model->date->format('d/m/Y');
            })
            ->editColumn('delivery_date', function ($model) {
                return $model->delivery_date->format('d/m/Y');
            })
            ->addColumn('action', function ($model) {
                return '<a href="' . route('dashboard.order.show', $model->id) . '" class="btn btn-sm btn-primary text-white"><i class="fa fa-eye" /></a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function checkUserViewed()
    {
    }
}
