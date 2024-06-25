<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Created()
 * @method static static InDesign()
 * @method static static InProduction()
 * @method static static Finished()
 * @method static static Shipping()
 * @method static static Pickup()
 * @method static static Cancelled()
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
