<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Discount;

class DiscountParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $discounts = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/ScontoMaggiorazione', false);
        foreach ($discounts as $discount) {
            $discountInstance = $this->extractDiscountInformationsFrom($discount);
            $this->digitalDocymentInstance->addDiscount($discountInstance);
        }
    }

    protected function extractDiscountInformationsFrom($discount): DiscountInterface
    {
        $discountInstance = new Discount();

        $value = $this->extractValueFromXmlElement($discount, 'Tipo');
        $discountInstance->setType($value);

        $value = $this->extractValueFromXmlElement($discount, 'Percentuale');
        $discountInstance->setPercentage($value);

        $value = $this->extractValueFromXmlElement($discount, 'Importo');
        $discountInstance->setAmount($value);

        return $discountInstance;
    }
}
