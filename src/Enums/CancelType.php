<?php

namespace Weble\FatturaElettronica\Enums;

/**
 * @method static self SC()
 * @method static self PR()
 * @method static self AB()
 * @method static self AC()
 * @method static self discount()
 * @method static self reward()
 * @method static self writeOff()
 * @method static self accessory()
 *
 */
class CancelType extends Enum
{
    protected static $map = [
        'discount' => 'SC',
        'reward' => 'PR',
        'writeOff' => 'AB',
        'accessory' => 'AC',
    ];

    protected static $descriptions = [
        'SC' => 'Sconto',
        'PR' => 'Premio',
        'AB' => 'Abbuono',
        'AC' => 'Spesa Accessoria'
    ];
}