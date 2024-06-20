<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()->paginate(10);
        return view('pages.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('pages.customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.max' => 'O campo nome deve ter no máximo 255 caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um email válido.',
            'email.max' => 'O campo email deve ter no mínimo 255 caracteres.',
        ]);

        Customer::query()->create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
        ]);

        session()->flash('success', 'Cliente adicionado com sucesso!');
        return redirect()->route('customer.index');
    }

    public function edit($id)
    {
        $customer = Customer::query()->findOrFail($id);
        return view('pages.customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.max' => 'O campo nome deve ter no mínimo 255 caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um email válido.',
            'email.max' => 'O campo email deve ter no mínimo 255 caracteres.',
        ]);

        $customer = Customer::query()->findOrFail($id);

        $customer->update([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
        ]);

        session()->flash('success', 'Cliente atualizado com sucesso!');
        return redirect()->route('customer.index');
    }

    public function destroy($id)
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->delete();
        session()->flash('success', 'Cliente excluído com sucesso!');
        return redirect()->route('customer.index');
    }

    public function filter(Request $request)
    {
        $this->validate($request, [
            'search' => 'string|max:255',
        ]);

        $customers = Customer::query()
            ->where('name', 'like', '%' . $request->get('search') . '%')
            ->orWhere('email', 'like', '%' . $request->get('search') . '%')
            ->orderBy('id', 'desc')
            ->get();

        if ($customers->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }
}
