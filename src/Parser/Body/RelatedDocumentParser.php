<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\RelatedDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\RelatedDocument;

abstract class RelatedDocumentParser extends AbstractBodyParser
{
    protected function extractRelatedDocumentInformationsFrom($order): RelatedDocumentInterface
    {
        $instance = new RelatedDocument();

        $value = (array) $this->extractValueFromXmlElement($order, 'RiferimentoNumeroLinea', false);
        foreach ($value as $reference) {
            $instance->addLineNumberReference($reference);
        }

        $value = $this->extractValueFromXmlElement($order, 'IdDocumento');
        $instance->setDocumentNumber($value);

        $value = $this->extractValueFromXmlElement($order, 'Data');
        $instance->setDocumentDate($value);

        $value = $this->extractValueFromXmlElement($order, 'NumItem');
        $instance->setLineNumber($value);

        $value = $this->extractValueFromXmlElement($order, 'CodiceCommessaConvenzione');
        $instance->setOrderCode($value);

        $value = $this->extractValueFromXmlElement($order, 'CodiceCUP');
        $instance->setCupCode($value);

        $value = $this->extractValueFromXmlElement($order, 'CodiceCIG');
        $instance->setCigCode($value);

        return $instance;
    }
}
