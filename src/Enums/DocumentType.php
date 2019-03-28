<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

/**
 * @method static self invoice()
 * @method static self advanceInvoice()
 * @method static self advanceBill()
 * @method static self creditNote()
 * @method static self debitNote()
 * @method static self bill()
 * @method static self TD01()
 * @method static self TD02()
 * @method static self TD03()
 * @method static self TD04()
 * @method static self TD05()
 * @method static self TD06()
 */
class DocumentType extends Enum
{
    protected static $map = [
        'invoice' => 'TD01',
        'advanceInvoice' => 'TD02',
        'advanceBill' => 'TD03',
        'creditNote' => 'TD04',
        'debitNote' => 'TD05',
        'bill' => 'TD06',
    ];

    protected static $descriptions = [
        'TD01' => 'Fattura',
        'TD02' => 'Acconto / Anticipo su Fattura',
        'TD03' => 'Acconto / Anticipo su Parcella',
        'TD04' => 'Nota di Credito',
        'TD05' => 'Nota di Debito',
        'TD06' => 'Parcella',
    ];
}