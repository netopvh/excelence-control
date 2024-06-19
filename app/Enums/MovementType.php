<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Created()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class MovementType extends Enum
{
    const Created = 'created';
    const InDesign = 'in_design';
    const InProduction = 'in_production';
    const Finished = 'finished';
    const Shipping = 'shipping';
    const Pickup = 'pickup';
    const Cancelled = 'cancelled';
}
