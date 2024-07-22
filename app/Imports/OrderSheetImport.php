<?php

namespace App\Imports;

use App\Enums\ActionType;
use App\Enums\MovementType;
use App\Enums\OriginType;
use App\Enums\StatusType;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderSheetImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
    use Importable, RemembersRowNumber;

    private ?Customer $customer = null;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->processRow($row->toArray());
        }
    }

    private function processRow(array $row)
    {
        Log::debug($row);
        if (!isset($row['cliente']) || is_null($row['cliente'])) {
            return;
        }

        $date = DateTime::createFromFormat('d/m/', $row['data']);
        if ($date && $date->format('d/m/') === $row['data']) {
            $row['data'] = $date->format('Y-m-d');
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
                'delivery_date' => $this->parseDate($row['entrega'])
            ])->save();

            $this->createOrderMovement($order, $row);
        }
        return $order;
    }

    private function createOrderProduct(Order $order, array $row)
    {
        $product = $this->findOrCreateProduct($row['produto']);

        $order->orderProducts()->create([
            'product_id' => $product->id,
            'qtd' => $row['qtd'],
            'in_stock' => $this->validateInStock($row['estoque']),
            'step' => $this->validateStep($row['arte'], $row['producao']),
            'production_date' => trim($row['producao']) ? $this->parseDate($row['producao']) : null,
            'arrived' => $row['estoque'] === 'S' || $row['estoque'] === 'OK' ? 'Y' : 'N',
            'status' => $this->validateStatus($row['arte']),
        ]);
    }

    private function createOrderMovement(Order $order, array $row)
    {
        $order->movements()->create([
            'action_date' => now(),
            'action_type' => ActionType::Register(),
            'action_user_id' => auth()->user()->id,
            'order_id' => $order->id,
            'origin' => OriginType::Order(),
            'movement_type' => MovementType::InDesign(),
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

    private function validateStep($status, $producao)
    {
        $status = trim($status);
        $producao = trim($producao);

        if (
            $status === 'Aprovado(C)' ||
            $status === 'Aprovado(D)' ||
            $status === 'Aprovado(R)' &&
            !$producao
        ) {
            return MovementType::InProduction();
        } elseif (
            $status === 'Aprovado(C)' ||
            $status === 'Aprovado(D)' ||
            $status === 'Aprovado(R)' &&
            $producao
        ) {
            return MovementType::InDesign();
        } else if ($status === 'Aguard Aprov') {
            return MovementType::InDesign();
        } else if ($status === 'Cancelado') {
            return MovementType::Cancelled;
        } else if ($status === 'Aguard Arte') {
            return MovementType::InDesign();
        }

        return MovementType::InDesign();
    }

    private function validateStatus($status)
    {
        if ($status === 'Aprovado(C)' || $status === 'Aprovado(D)' || $status === 'Aprovado(R)') {
            return StatusType::Approved();
        } else if ($status === 'Aguard Aprov') {
            return StatusType::WaitingApproval();
        } else if ($status === 'Aguard Arte') {
            return StatusType::WaitingDesign();
        }

        return StatusType::WaitingDesign();
    }

    private function validateInStock($data): string
    {
        if ($data === 'S' || $data === 'OK') {
            return 'yes';
        } else if ($data === 'E') {
            return 'no';
        }

        return 'no';
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
