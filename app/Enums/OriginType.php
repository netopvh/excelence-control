<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Order()
 * @method static static Purchase()
 */
final class OriginType extends Enum
{
    const Order = 'O';
    const Purchase = 'P';
    const Production = 'F';
}
