<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Register()
 * @method static static Ciencia()
 */
final class ActionType extends Enum
{
    const Register = 'R';
    const Ciencia = 'C';
}
