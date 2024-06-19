<?php

use App\Enums\MovementType;
use App\Enums\StatusType;

if (!function_exists('status_type')) {
    function status_type(string $status)
    {
        if ($status == StatusType::Approved()) {
            return 'Aprovado';
        } elseif ($status == StatusType::WaitingApproval()) {
            return 'Aguard. Aprov';
        } elseif ($status == StatusType::WaitingDesign()) {
            return 'Aguard. Arte';
        } else {
            return 'Não Identificado';
        }
    }
}

if (!function_exists('get_step')) {
    function get_step(string $step)
    {
        if ($step == MovementType::Created()) {
            return 'Novo';
        } elseif ($step == MovementType::InDesign()) {
            return 'Design e Arte';
        } elseif ($step == MovementType::InProduction) {
            return 'Produção';
        } elseif ($step == MovementType::Finished) {
            return 'Concluído';
        } elseif ($step == MovementType::Shipping) {
            return 'Para Entrega';
        } elseif ($step == MovementType::Pickup) {
            return 'Para Retirada';
        } elseif ($step == MovementType::Cancelled) {
            return 'Cancelado';
        } else {
            return 'Não Identificado';
        }
    }
}
