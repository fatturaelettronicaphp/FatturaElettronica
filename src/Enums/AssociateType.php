<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

/**
 * @method static self SU()
 * @method static self SM()
 * @method static self single()
 * @method static self multiple()
 *
 */
class AssociateType extends Enum
{
    protected static $map = [
        'single'   => 'SU',
        'multiple' => 'SM',
    ];

    protected static $descriptions = [
        'SU' => 'Socio Unico',
        'SM' => 'Più Soci',
    ];
}
