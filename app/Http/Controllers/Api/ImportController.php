<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrderImport;
use App\Jobs\ImportProductsJob;
use Automattic\WooCommerce\Client;
use Codexshaper\WooCommerce\Facades\Product as WooProduct;

class ImportController extends Controller
{
    protected $woocommerce;

    public function __construct(Client $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    public function import(Request $request)
    {
        $request->validate(
            [
                'file' => 'required|mimes:xlsx,xls,csv'
            ],
            [
                'file.mimes' => 'O arquivo deve ser do tipo xlsx, xls ou csv',
                'file.required' => 'O arquivo deve ser selecionado'
            ]
        );

        try {
            Excel::import(new OrderImport(), $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Importação realizada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                // 'message' => 'Ocorreu um erro de formatação de datas, verique os campos de data do seu arquivo excel.'
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importProducts()
    {
        ImportProductsJob::dispatch($this->woocommerce);
        return response()->json(['success' => true, 'message' => 'Importação de produtos iniciada.']);
    }
}
