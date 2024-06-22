<?php

namespace App\DataTables;

use App\Enums\MovementType;
use App\Enums\StatusType;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;

class OrderDataTableEditor extends DataTablesEditor
{
    protected $model = Order::class;

    /**
     * Get create action validation rules.
     */
    public function createRules(): array
    {
        return [
            'step' => ['required', Rule::enum(MovementType::class)],
            'status' => ['required', Rule::enum(StatusType::class)],
        ];
    }

    /**
     * Get edit action validation rules.
     */
    public function editRules(Model $model): array
    {
        return [
            'step' => ['required', Rule::enum(MovementType::class)],
            'status' => ['required', Rule::enum(StatusType::class)],
        ];
    }

    /**
     * Get remove action validation rules.
     */
    public function removeRules(Model $model): array
    {
        return [];
    }

    /**
     * Event hook that is fired after `creating` and `updating` hooks, but before
     * the model is saved to the database.
     */
    public function saving(Model $model, array $data): array
    {
        return $data;
    }

    /**
     * Event hook that is fired after `created` and `updated` events.
     */
    public function saved(Model $model, array $data): Model
    {
        // do something after saving the model

        return $model;
    }
}
