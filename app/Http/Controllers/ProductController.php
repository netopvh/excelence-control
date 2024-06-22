<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $products = Product::query()
            ->select('id', 'name')
            ->where('name', 'LIKE', "%{$q}%")
            ->get();

        return response()->json($products);
    }
}
