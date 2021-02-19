<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

/**
 * @method static self TP01()
 * @method static self TP02()
 * @method static self TP03()
 * @method static self full()
 * @method static self advanced()
 * @method static self installment()
 */
class PaymentTerm extends Enum
{
    protected static $map = [
        'installment' => 'TP01',
        'full'        => 'TP02',
        'advanced'    => 'TP03',
    ];

    protected static $descriptions = [
        'TP01' => 'Pagamento a Rate',
        'TP02' => 'Pagamento Completo',
        'TP03' => 'Anticipo',
    ];
}
