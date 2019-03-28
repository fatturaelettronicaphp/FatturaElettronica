<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

/**
 * @method static self SC()
 * @method static self MG()
 * @method static self discount()
 * @method static self increase()
 */
class DiscountType extends Enum
{
    protected static $map = [
        'discount' => 'SC',
        'increase' => 'MG'
    ];

    protected static $descriptions = [
        'SC' => 'Sconto',
        'MG' => 'Maggiorazione',
    ];
}