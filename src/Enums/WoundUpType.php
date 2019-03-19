<?php

namespace Weble\FatturaElettronica\Enums;

/**
 * @method static self LS()
 * @method static self LN()
 * @method static self normal()
 * @method static self woundUp()
 */
class WoundUpType extends Enum
{
    protected static $map = [
        'normal' => 'LN',
        'woundUp' => 'LS'
    ];

    protected static $descriptions = [
        'LN' => 'Non in Liquidazione',
        'LS' => 'In Liquidazione'
    ];
}
