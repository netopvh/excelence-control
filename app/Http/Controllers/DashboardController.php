<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index() : \Illuminate\View\View
    {
        $approved = Order::query()->where('status', 'aprovado')->count();
        $waitingApproval = Order::query()->where('status', 'aguard. aprov')->count();
        $waitingArt = Order::query()->where('status', 'aguard. arte')->count();

        return view('dashboard', compact('approved', 'waitingApproval', 'waitingArt'));
    }

    public function list(Request $request)
    {
        $model = Order::query()->with(['customer', 'orderProducts']);

        return DataTables::of($model)
            ->filter(function ($query) use ($request) {

                if (trim($request->get('search')['value']) !== '') {
                    $query->whereHas('customer', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->get('search')['value'] . '%');
                    })
                        ->orWhere('number', 'like', '%' . $request->get('search')['value'] . '%')
                        ->orWhere('employee', 'like', '%' . $request->get('search')['value'] . '%');
                }

                if ($request->get('status') !== 'all') {
                    $query->where('status', $request->get('status'));
                }

                if ($request->get('month') !== 'all') {
                    $query->whereMonth('date', $request->get('month'));
                }

                if (trim($request->get('from')) !== '' && trim($request->get('to')) !== '') {
                    $query->whereDate('date', '>=', Carbon::createFromFormat('d/m/Y', $request->get('from')))
                        ->whereDate('date', '<=', Carbon::createFromFormat('d/m/Y', $request->get('to')));
                }
            })
            ->editColumn('date', function ($model) {
                return $model->date->format('d/m/Y');
            })
            ->editColumn('delivery_date', function ($model) {
                return $model->delivery_date->format('d/m/Y');
            })
            ->editColumn('customer.name', function ($model) {
                return strtoupper($model->customer->name);
            })
            ->editColumn('status', function ($model) {
                if ($model->status === 'aprovado') {
                    return '<span class="badge bg-success">Aprovado</span>';
                } else if ($model->status === 'aguard. aprov') {
                    return '<span class="badge bg-warning">Aguardando Aprov.</span>';
                } else if ($model->status === 'aguard. arte') {
                    return '<span class="badge bg-info">Aguardando Arte</span>';
                } else {
                    return '<span class="badge bg-black-50">Não definido</span>';
                }
            })
            ->editColumn('employee', function ($model) {
                return $model->employee ? strtoupper($model->employee) : 'Não definido';
            })
            ->editColumn('arrived', function ($model) {
                return $model->arrived ? '<span class="badge bg-success">Chegou</span>' : '';
            })
            ->addColumn('action', function ($model) {
                return '<a href="' . route('dashboard.order.show', $model->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye" /></a>';
            })
            ->with('totalApproved', Order::query()->where('status', 'aprovado')->count())
            ->with('totalWaitingApproval', Order::query()->where('status', 'aguard. aprov')->count())
            ->with('totalWaitingArt', Order::query()->where('status', 'aguard. arte')->count())
            ->rawColumns(['status', 'arrived', 'action'])
            ->make(true);
    }
}
