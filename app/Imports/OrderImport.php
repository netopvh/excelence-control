<?php

namespace App\Imports;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OrderImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
    use Importable, RemembersRowNumber;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (isset($row['cliente'])) {
                $customer = Customer::where('name', $row['cliente'])->first();

                if (! $customer) {
                    if ($row['cliente'] != null) {
                        $customer = Customer::create([
                            'name' => $row['cliente']
                        ]);

                        $order = $customer->orders()->where('number', $row['pedido'])->first();

                        if (! $order) {

                            $dateExplode = explode('-', $row['entrega']);
                            $deliveryDate = date("Y") . '-' . $dateExplode[1] . '-' . $dateExplode[0];

                            $order = $customer->orders()->create([
                                'employee' => $row['funcionario'],
                                'date' => (gettype($row['data']) === 'string') ? Carbon::now()->format('Y-m-d') : Date::excelToDateTimeObject($row['data']),
                                'number' => $row['pedido'],
                                'status' => $row['status'],
                                'delivery_date' => $deliveryDate,
                                'arrived' => $row['mercadoria'] === 'CHEGOU' ? true : false,
                            ]);

                            $order->orderProducts()->create([
                                'name' => $row['produto'],
                                'qtd' => $row['quantidade'],
                            ]);
                        } else {
                            $order->orderProducts()->create([
                                'name' => $row['produto'],
                                'qtd' => $row['quantidade'],
                            ]);
                        }

                    }
                } else {
                    $order = $customer->orders()->where('number', $row['pedido'])->first();

                    if (! $order) {
                        $dateExplode = explode('-', $row['entrega']);
                        $deliveryDate = date("Y") . '-' . $dateExplode[1] . '-' . $dateExplode[0];

                        $order = $customer->orders()->create([
                            'employee' => $row['funcionario'],
                            'date' => (gettype($row['data']) === 'string') ? Carbon::now()->format('Y-m-d') : Date::excelToDateTimeObject($row['data']),
                            'number' => $row['pedido'],
                            'status' => $row['status'],
                            'delivery_date' => $deliveryDate,
                            'arrived' => $row['mercadoria'] === 'CHEGOU' ? true : false,
                        ]);

                        $order->orderProducts()->create([
                            'name' => $row['produto'],
                            'qtd' => $row['quantidade'],
                        ]);
                    } else {
                        $order->orderProducts()->create([
                            'name' => $row['produto'],
                            'qtd' => $row['quantidade'],
                        ]);
                    }
                }
            }
        }
    }

    public function batchSize() : int
    {
        return 269;
    }

    /**
     * @param array $row
     */
    // public function model(array $row)
    // {

    //     return new Customer([
    //         'name' => $row['cliente']
    //     ]);

    // $order = $customer->orders()->create([
    //     'employee' => $row[6],
    //     'date' => (gettype($row[0]) === 'string') ? Carbon::now()->format('Y-m-d') : Date::excelToDateTimeObject($row[0]),
    //     'number' => $row[1],
    //     'status' => $row[5],
    // ]);

    // return $order->orderProducts()->create([
    //     'name' => $row[4],
    //     'qtd' => $row[3],
    // ]);
    // }
}
