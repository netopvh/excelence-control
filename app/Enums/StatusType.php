<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Approved()
 * @method static static WaitingApproval()
 * @method static static WaitingDesign()
 */
final class StatusType extends Enum
{
    const Approved = 'approved';
    const WaitingApproval = 'waiting_approval';
    const WaitingDesign = 'waiting_design';
}
