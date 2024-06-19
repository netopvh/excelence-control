<?php

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
