<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

/**
 * @method static self empty()
 * @method static self foreign()
 */
class RecipientCode extends Enum
{
    public static $map = [
        'empty'   => '0000000',
        'foreign' => 'XXXXXXX',
    ];
}
