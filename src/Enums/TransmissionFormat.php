<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

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

    protected static $descriptions = [
        'FPA12' => 'Pubblica Amministrazione (P.A.)',
        'FPR12' => 'Privati (B2B / B2C)'
    ];
}