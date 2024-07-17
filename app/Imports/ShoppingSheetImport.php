<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ShoppingSheetImport implements ToCollection, WithBatchInserts
{
    use Importable, RemembersRowNumber;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $row)
    {
        foreach ($row as $row) {
            $this->processRow($row->toArray());
        }
    }

    private function processRow(array $row): void
    {
    }

    public function batchSize(): int
    {
        return 269;
    }
}
