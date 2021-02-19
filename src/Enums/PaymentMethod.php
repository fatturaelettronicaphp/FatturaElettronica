<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

/**
 * @method static self MP01()
 * @method static self MP02()
 * @method static self MP03()
 * @method static self MP04()
 * @method static self MP05()
 * @method static self MP06()
 * @method static self MP07()
 * @method static self MP08()
 * @method static self MP09()
 * @method static self MP10()
 * @method static self MP11()
 * @method static self MP12()
 * @method static self MP13()
 * @method static self MP14()
 * @method static self MP15()
 * @method static self MP16()
 * @method static self MP17()
 * @method static self MP18()
 * @method static self MP19()
 * @method static self MP20()
 * @method static self MP21()
 * @method static self MP22()
 */
class PaymentMethod extends Enum
{
    protected static $descriptions = [
        'MP01' => 'contanti',
        'MP02' => 'assegno',
        'MP03' => 'assegno circolare',
        'MP04' => 'contanti presso Tesoreria',
        'MP05' => 'bonifico',
        'MP06' => 'vaglia cambiario',
        'MP07' => 'bollettino bancario',
        'MP08' => 'carta di pagamento',
        'MP09' => 'RID',
        'MP10' => 'RID utenze',
        'MP11' => 'RID veloce',
        'MP12' => 'RIBA',
        'MP13' => 'MAV',
        'MP14' => 'quietanza erario',
        'MP15' => 'giroconto su conti di contabilità speciale',
        'MP16' => 'domiciliazione bancaria',
        'MP17' => 'domiciliazione postale',
        'MP18' => 'bollettino di c/c postale',
        'MP19' => 'SEPA Direct Debit',
        'MP20' => 'SEPA Direct Debit CORE',
        'MP21' => 'SEPA Direct Debit B2B',
        'MP22' => 'Trattenuta su somme già riscosse',
    ];
}
