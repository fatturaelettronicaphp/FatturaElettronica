<?php

namespace Weble\FatturaElettronica\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self FPA12()
 * @method static self FPR12()
 * @method static self b2b()
 * @method static self pa()
 */
class TransmissionFormat extends Enum
{
    protected static $map = [
        'FPA12' => 'FPA12',
        'FPR12' => 'FPR12',
        'b2b' => 'FPR12',
        'pa' => 'FPA12',
    ];
}