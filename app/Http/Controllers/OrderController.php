<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $customers = Customer::query()->orderBy('name')->get();

        return view('pages.order.create', compact('customers'));
    }

    public function show($id)
    {
        $order = Order::query()->with(['customer', 'orderProducts'])->findOrFail($id);

        return view('pages.order.show', compact('order'));
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
        ]);


        $customer = Customer::query()->findOrFail($request->get('customer_id'));

        $order = $customer->orders()->create([
            'date' => Carbon::createFromFormat('d/m/Y', $request->get('date'))->format('Y-m-d'),
            'number' => $request->get('number'),
            'delivery_date' => Carbon::createFromFormat('d/m/Y', $request->get('delivery_date'))->format('Y-m-d'),
            'status' => 'aguard. arte',
        ]);

        foreach ($request->get('product') as $product) {
            $order->orderProducts()->create([
                'name' => $product['name'],
                'qtd' => $product['qtd']
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
            'message' => 'Preview enviado com sucesso!',
        ]);
    }

    public function uploadDesign(Request $request, $id)
    {
        $request->validate([
            'design' => 'required|mimes:pdf|max:6144',
        ], [
            'design.required' => 'O campo arquivo é obrigatório.',
            'design.mimes' => 'O campo arquivo deve ser um PDF.',
            'design.max' => 'O campo arquivo deve ter no máximo 2MB.',
        ]);

        $order = Order::query()->findOrFail($id);

        $imageName = time() . '.' . $request->file('design')->extension();

        $request->file('design')->storeAs('', $imageName, ['disk' => 'design']);

        $order->update([
            'design_file' => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Design enviado com sucesso!',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aprovado,aguard. aprov,aguard. arte',
        ], [
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'O campo status deve ser um dos seguintes valores: Aprovado, Aguard. Aprov, Aguard. Arte.',
        ]);

        $order = Order::query()->findOrFail($id);

        $order->update([
            'status' => $request->get('status'),
        ]);

        session()->flash('success', 'Status atualizado com sucesso!');
        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!',
        ]);
    }

    public function updateEmployee(Request $request, $id)
    {
        $request->validate([
            'employee' => 'required|string|max:255',
        ], [
            'employee.string' => 'O campo funcionário deve ser uma string.',
            'employee.max' => 'O campo funcionário deve ter no máximo 255 caracteres.',
        ]);

        $order = Order::query()->findOrFail($id);

        $order->update([
            'employee' => $request->get('employee'),
        ]);

        session()->flash('success', 'Arte Finalista atualizado com sucesso!');
        return response()->json([
            'success' => true,
            'message' => 'Arte Finalista atualizado com sucesso!',
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
}
