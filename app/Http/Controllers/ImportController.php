<?php

namespace App\Http\Controllers;

use App\Imports\OrderImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('pages.import');
    }

    public function import(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            (new OrderImport)->import($request->file('file'));

            return response()->json([
                'message' => 'Importação realizada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro de formatação de datas, verique os campos de data do seu arquivo excel.'
            ], 500);
        }
    }
}
