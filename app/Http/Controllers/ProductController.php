<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function autocomplete(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
        ], [
            'query.required' => 'O campo busca é obrigatório.',
            'query.string' => 'O campo busca deve ser uma string.',
            'query.max' => 'O campo busca deve ter no máximo 255 caracteres.',
        ]);

        $q = $request->get('query');

        $products = OrderProduct::query()
            ->where('name', 'LIKE', "%{$q}%")
            ->get();

        return response()->json($products);
    }
}
