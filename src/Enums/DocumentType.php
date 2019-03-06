<?php

namespace Weble\FatturaElettronica\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self invoice()
 * @method static self advanceInvoice()
 * @method static self advanceBill()
 * @method static self creditNote()
 * @method static self debitNote()
 * @method static self bill()
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
}