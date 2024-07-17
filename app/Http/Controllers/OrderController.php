<?php

namespace App\Http\Controllers;

use App\Enums\ActionType;
use App\Enums\MovementType;
use App\Enums\OriginType;
use App\Enums\StatusType;
use App\Events\OrderStepUpdated;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function json(Request $request)
    {
        $model = Order::query()
            ->with(['customer', 'orderProducts.product', 'employee']);

        return DataTables::of($model)
            ->filter(function ($query) use ($request) {

                if ($searchValue = trim($request->get('search')['value'])) {
                    $query->filterBySearch($searchValue);
                }

                if ($step = $request->get('step') !== 'all') {
                    $query->where('step', $step);
                }

                if ($month = $request->get('month') !== 'all') {
                    $query->whereMonth('date', $month);
                }

                if ($type = trim($request->get('type')) && $date = trim($request->get('date'))) {
                    $query->whereDate($type, $date);
                }
            })
            ->order(function ($query) use ($request) {
                if (isset($request['order'])) {
                    foreach ($request['order'] as $order) {
                        $columnName = $order['name'] ?? null;
                        $columnDir = $order['dir'] ?? 'asc';

                        if ($columnName) {
                            if ($columnName == 'customer.name') {
                                $query->whereHas('customer', function ($subQuery) use ($columnDir) {
                                    $subQuery->orderBy('name', $columnDir);
                                });
                            } else {
                                $query->orderBy($columnName, $columnDir);
                            }
                        } else {
                            $query->orderBy('date', 'desc');
                        }
                    }
                }
            })
            ->editColumn('date', function (Order $model) {
                return $model->date_formatted;
            })
            ->editColumn('delivery_date', function (Order $model) {
                return $model->delivery_date_formatted;
            })
            ->editColumn('customer.name', function (Order $model) {
                return ucwords($model->customer->name);
            })
            ->editColumn('arrived', function (Order  $model) {
                return $model->arrived ? '<span class="badge bg-success">Chegou</span>' : '';
            })
            ->addColumn('action', function (Order $model) {

                $buttons = '<div class="btn-group">';

                $buttons .= '<a href="' . route('dashboard.order.show', ['id' => $model->id]) . '" class="btn btn-primary btn-sm">
                    <i class="fa fa-eye"></i>
                </a>';

                if (auth()->user()->hasRole('superadmin')) {
                    $buttons .= '<a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delete-order-' . $model->id . '">
                        <i class="fa fa-trash"></i>
                    </a>';
                }

                $buttons .= '</div>';

                return $buttons;
            })
            ->setRowId(function (Order $model) {
                return $model->id;
            })
            ->rawColumns(['step', 'arrived', 'action'])
            ->make(true);
    }

    public function index()
    {
        return view('pages.order.list');
    }

    public function jsonKanban()
    {
        $model = Order::query()
            ->with(['customer', 'orderProducts'])
            ->orderBy('created_at', 'asc');

        return response()->json($model->get()->map(function ($model) {
            return [
                'id' => $model->id,
                'number' => $model->number,
                'customer' => $model->customer->name,
                'date' => $model->date->format('d/m/Y'),
                'status' => $model->status,
                'step' => $model->step,
            ];
        }));
    }

    public function kanban()
    {
        return view('pages.order.kanban');
    }

    public function updateKanban(Request $request, $id)
    {
        $order = Order::query()->findOrFail($id);

        $order->update([
            'step' => $request->get('step'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!',
        ]);
    }

    public function create()
    {
        $customers = Customer::query()->orderBy('name')->get();

        return view('pages.order.create', compact('customers'));
    }

    public function show($id)
    {
        $order = Order::query()->with(['customer', 'orderProducts.product'])->findOrFail($id);

        $users = User::query()->whereHas('roles', function ($query) {
            $query->where('name', 'design');
        })
            ->where('name', '!=', 'Dayane Azevedo')
            ->get();

        $status = [
            'approved'          => 'Aprovado',
            'waiting_approval'  => 'Aguard. Aprov',
            'waiting_design'    => 'Aguard. Arte',
        ];

        $step = [
            'created'           => 'Novo',
            'in_design'         => 'Design e Arte',
            'in_production'     => 'Produção',
            'finished'          => 'Concluído',
            'shipping'          => 'Para Entrega',
            'pickup'            => 'Para Retirada',
            'cancelled'         => 'Cancelado',
        ];

        return view('pages.order.show', compact('order', 'users', 'status', 'step'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required',
            'number' => 'required|numeric',
            'delivery_date' => 'required',
            'product' => 'required|array',
            'product.*.name' => 'required|string|max:255',
            'product.*.qtd' => 'required|numeric|min:0.01',
            'product.*.in_stock' => 'required',
            'product.*.supplier' => 'nullable|string|max:255',
            'product.*.obs' => 'nullable|string|max:255',
        ], [
            'customer_id.required' => 'O campo cliente é obrigatório.',
            'customer_id.exists' => 'O campo cliente deve ser um cliente válido.',
            'date.required' => 'O campo emissão é obrigatório.',
            'date.date' => 'O campo emissão deve ser uma data válida.',
            'number.required' => 'O campo pedido é obrigatório.',
            'number.numeric' => 'O campo pedido deve ser um número.',
            'delivery_date.required' => 'O campo data de entrega é obrigatório.',
            'delivery_date.date' => 'O campo data de entrega deve ser uma data válida.',
            'product.required' => 'O campo produto é obrigatório.',
            'product.array' => 'O campo produto deve ser um array.',
            'product.*.name.required' => 'O campo nome é obrigatório.',
            'product.*.name.string' => 'O campo nome deve ser uma string.',
            'product.*.name.max' => 'O campo nome deve ter no máximo 255 caracteres.',
            'product.*.qtd.required' => 'O campo quantidade é obrigatório.',
            'product.*.qtd.numeric' => 'O campo quantidade deve ser um número.',
            'product.*.qtd.min' => 'O campo quantidade deve ser no mínimo 0.01.',
            'product.*.in_stock.required' => 'O campo estoque é obrigatório.',
        ]);



        $customer = Customer::query()->findOrFail($request->get('customer_id'));

        $order = $customer->orders()->create([
            'employee_id' => auth()->user()->id,
            'date' => Carbon::createFromFormat('d/m/Y', $request->get('date'))->format('Y-m-d'),
            'number' => $request->get('number'),
            'delivery_date' => Carbon::createFromFormat('d/m/Y', $request->get('delivery_date'))->format('Y-m-d'),
            'step' => MovementType::InDesign(),
        ]);

        $order->movements()->create([
            'action_date' => now(),
            'action_type' => ActionType::Register(),
            'action_user_id' => auth()->user()->id,
            'movement_type' => MovementType::InDesign(),
            'origin' => OriginType::Order(),
        ]);

        foreach ($request->get('product') as $product) {
            $order->orderProducts()->create([
                'product_id' => $this->findProduct($product['name']),
                'qtd' => $product['qtd'],
                'in_stock' => $product['in_stock'],
                'supplier' => $product['supplier'],
                'link' => $product['link'],
                'obs' => $product['obs'],
                'arrived' => 'N',
                'status' => StatusType::WaitingDesign(),
            ]);
        }

        session()->flash('success', 'Pedido criado com sucesso!');
        return redirect()->back();
    }

    public function uploadPreview(Request $request, $id)
    {
        $request->validate([
            'preview' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ], [
            'preview.required' => 'O campo arquivo é obrigatório.',
            'preview.image' => 'O campo arquivo deve ser uma imagem.',
            'preview.mimes' => 'O campo arquivo deve ser uma imagem válida.',
            'preview.max' => 'O campo arquivo deve ter no máximo 2MB.',
        ]);

        $order = Order::query()->findOrFail($id);

        $imageName = time() . '.' . $request->file('preview')->extension();

        $request->file('preview')->storeAs('', $imageName, ['disk' => 'preview']);

        $order->update([
            'preview' => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'fileUrl' => asset('preview/' . $order->preview),
            'message' => 'Preview enviado com sucesso!',
        ]);
    }

    public function removeDesign($id)
    {
        $order = Order::query()->findOrFail($id);

        $order->update([
            'design_file' => null,
            'preview' => null
        ]);

        return redirect()->back()->with('success', 'Design removido com sucesso!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,waiting_approval,waiting_design',
        ], [
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'O campo status deve ser um dos seguintes valores: Aprovado, Aguard. Aprov, Aguard. Arte.',
        ]);

        $order = Order::query()->findOrFail($id);

        $order->update([
            'status' => $request->get('status'),
        ]);

        return response()->json([
            'success' => true,
            'status' => status_type($request->get('status')),
            'message' => 'Status atualizado com sucesso!',
        ]);
    }

    public function updateEmployee(Request $request, $id)
    {
        $request->validate([
            'employee' => 'required|exists:users,name',
        ], [
            'employee.required' => 'O campo funcionário é obrigatório.',
            'employee.exists' => 'O campo funcionário deve existir.',
        ]);

        $order = Order::query()->findOrFail($id);
        $employee = User::query()->where('name', $request->get('employee'))->firstOrFail();

        $order->update([
            'employee_id' => $employee->id,
        ]);

        session()->flash('success', 'Arte Finalista atualizado com sucesso!');
        return response()->json([
            'success' => true,
            'message' => 'Arte Finalista atualizado com sucesso!',
        ]);
    }

    public function updateDesigner(Request $request, $id)
    {
        $request->validate([
            'designer' => 'required|exists:users,name',
        ], [
            'designer.required' => 'O campo funcionário é obrigatório.',
            'designer.exists' => 'O campo funcionário deve existir.',
        ]);

        $order = Order::query()->findOrFail($id);
        $employee = User::query()->where('name', $request->get('designer'))->firstOrFail();

        $order->update([
            'designer_id' => $employee->id,
        ]);

        return response()->json([
            'success' => true,
            'employee' => $employee,
            'message' => 'Arte finalista alterado com sucesso!',
        ]);
    }

    public function updateArrived(Request $request, $id)
    {
        $request->validate([
            'arrived' => 'required|boolean',
        ], [
            'arrived.required' => 'O campo chegou é obrigatório.',
            'arrived.boolean' => 'O campo chegou deve ser um booleano.',
        ]);

        $order = Order::query()->findOrFail($id);

        $order->update([
            'arrived' => $request->get('arrived'),
        ]);

        session()->flash('success', 'Chegou atualizado com sucesso!');
        return response()->json([
            'success' => true,
            'message' => 'Chegou atualizado com sucesso!',
        ]);
    }

    public function updateStep(Request $request, $id)
    {
        $request->validate([
            'step' => 'required|in:created,in_design,in_production,finished,shipping,pickup,cancelled',
        ], [
            'step.required' => 'O campo chegou é obrigatório.',
            'step.in' => 'O campo não possui o valor esperado.',
        ]);

        $order = Order::query()->findOrFail($id);

        $order->update([
            'step' => $request->get('step'),
        ]);

        event(new OrderStepUpdated($order));

        return response()->json([
            'success' => true,
            'step' => get_step($order->step),
            'message' => 'Etapa atualizada com sucesso!',
        ]);
    }

    private function findProduct(string $name): int
    {
        $product = Product::query()->where('name', trim($name))->firstOrFail();

        return $product->id;
    }
}
