<?php

namespace Weble\FatturaElettronica\Enums;

/**
 * @method static self N1()
 * @method static self N2()
 * @method static self N3()
 * @method static self N4()
 * @method static self N5()
 * @method static self N6()
 * @method static self N7()
 */
class VatNature extends Enum
{
    protected static $descriptions = [
        'N1' => 'escluse ex art. 15',
        'N2' => 'non soggette',
        'N3' => 'non imponibili',
        'N4' => 'esenti',
        'N5' => 'regime del margine / IVA non esposta in fattura',
        'N6' => 'inversione contabile (per le operazioni in reverse charge ovvero nei casi di autofatturazione per acquisti extra UE di servizi ovvero per importazioni di beni nei soli casi previsti)',
        'N7' => 'IVA assolta in altro stato UE (vendite a distanza ex art. 40 c. 3 e 4 e art. 41 c. 1 lett. b,  DL 331/93; prestazione di servizi di telecomunicazioni, tele-radiodiffusione ed elettronici ex art. 7-sexies lett. f, g, art. 74-sexies DPR 633/72)',
    ];
}