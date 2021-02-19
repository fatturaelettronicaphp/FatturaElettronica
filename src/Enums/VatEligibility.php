<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

/**
 * @method static self I()
 * @method static self D()
 * @method static self S()
 * @method static self immediately()
 * @method static self deferred()
 * @method static self split()
 */
class VatEligibility extends Enum
{
    protected static $map = [
        'immediately' => 'I',
        'deferred'    => 'D',
        'split'       => 'S',
    ];

    protected static $descriptions = [
        'I' => 'IVA ad esigibilità immediata',
        'D' => 'IVA ad esigibilità differita',
        'S' => 'Scissione dei Pagamenti',
    ];
}
