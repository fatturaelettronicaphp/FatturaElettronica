<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

/**
 * @method static self RT01()
 * @method static self RT02()
 * @method static self person()
 * @method static self company()
 */
class DeductionType extends Enum
{
    protected static $map = [
        'person' => 'RT01',
        'company' => 'RT02'
    ];

    protected static $descriptions = [
        'RT01' => 'Ritenuta Persone Fisiche',
        'RT02' => 'Ritenuta Persone Giuridiche',
    ];
}