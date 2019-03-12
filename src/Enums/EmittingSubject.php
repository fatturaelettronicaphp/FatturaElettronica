<?php

namespace Weble\FatturaElettronica\Enums;

/**
 * @method static self CC()
 * @method static self TZ()
 * @method static self supplier()
 * @method static self thirdParty()
 */
class EmittingSubject extends Enum
{
    protected static $map = [
        'supplier' => 'CC',
        'thirdParty' => 'TZ'
    ];

    protected static $descriptions = [
        'CC' => 'Cessionario / Committente',
        'TZ' => 'Terzo'
    ];
}