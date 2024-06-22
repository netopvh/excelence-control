<?php

namespace App\Imports;

use App\Enums\MovementType;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OrderImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
    use Importable, RemembersRowNumber;

    private ?Customer $customer = null;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->processRow($row->toArray());
        }
    }

    private function processRow(array $row)
    {
        if (!isset($row['cliente']) || is_null($row['cliente'])) {
            return;
        }

        $this->customer = Customer::firstOrCreate(['name' => $row['cliente']]);
        $order = $this->findOrCreateOrder($row);
        $this->createOrderProduct($order, $row);
    }

    private function findOrCreateOrder(array $row): Order
    {
        $order = $this->customer->orders()->firstOrNew(['number' => $row['pedido']]);

        if (!$order->exists) {
            $order->fill([
                'employee_id' => $this->findUser($row['vendedor']),
                'date' => $this->parseDate($row['data']),
                'step' => $this->validateStep($row['arte']),
                'delivery_date' => $this->parseDate($row['entrega']),
            ])->save();
        }
        return $order;
    }

    private function createOrderProduct(Order $order, array $row)
    {
        $product = $this->findOrCreateProduct($row['produto']);

        $order->orderProducts()->create([
            'product_id' => $product->id,
            'qtd' => $row['qtd'],
        ]);
    }

    private function findOrCreateProduct($productName): Product
    {
        return Product::firstOrCreate(['name' => $productName]);
    }

    private function parseDate($date)
    {
        if (is_numeric($date)) {
            $convertDateTime = Date::excelToDateTimeObject($date);
            if ($convertDateTime instanceof \DateTime) {
                return Carbon::instance($convertDateTime)->format('Y-m-d');
            }
        }
        return $this->parseDeliveryDate($date);
    }

    private function parseDeliveryDate($date)
    {
        $dateParts = explode('/', $date);
        if (count($dateParts) == 2) {
            $day = $dateParts[0];
            $month = $dateParts[1];
            $year = date('Y');
            return Carbon::createFromFormat('d/m/Y', "$day/$month/$year")->format('Y-m-d');
        }
        return $date;
    }

    private function getDeliveryDate($date)
    {
        $dateParts = explode('-', $date);
        return date('Y') . '-' . $dateParts[1] . '-' . $dateParts[0];
    }

    private function validateStep($status)
    {
        $status = trim($status);

        if ($status === 'Aprovado(C)' || $status === 'Aprovado(D)' || $status === 'Aprovado(R)') {
            return MovementType::InDesign;
        } else if ($status === 'Aguard Aprov') {
            return MovementType::Created;
        } else if ($status === 'Cancelado') {
            return MovementType::Cancelled;
        } else if ($status === 'Aguard Arte') {
            return MovementType::Created;
        }

        return MovementType::Created;
    }

    private function findUser($name)
    {
        $user = User::query()->where('name', 'like', '%' . $name . '%')->first();

        if (is_null($user)) {
            return null;
        }

        return $user->id;
    }

    public function batchSize(): int
    {
        return 269;
    }
}
