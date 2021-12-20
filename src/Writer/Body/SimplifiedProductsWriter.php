<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Body;

use FatturaElettronicaPhp\FatturaElettronica\SimplifiedLine;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;

class SimplifiedProductsWriter extends AbstractBodyWriter
{
    protected function performWrite()
    {
        /** @var SimplifiedLine[] $lines */
        $lines = $this->body->getSimplifiedLines();

        if (count($lines) <= 0) {
            return;
        }

        foreach ($lines as $line) {
            $datiBeniServizi = $this->xml->addChild('DatiBeniServizi');

            $datiBeniServizi->addChild('Descrizione', SimpleXmlExtended::sanitizeText($line->getDescription()));
            $datiBeniServizi->addChild('Importo', SimpleXmlExtended::sanitizeFloat($line->getTotal()));

            if ($line->getTaxAmount() || $line->getTaxPercentage()) {
                $datiIVA = $datiBeniServizi->addChild('DatiIVA');

                if ($line->getTaxAmount()) {
                    $datiIVA->addChild('Imposta', SimpleXmlExtended::sanitizeFloat($line->getTaxAmount()));
                }

                if ($line->getTaxPercentage()) {
                    $datiIVA->addChild('Aliquota', SimpleXmlExtended::sanitizeFloat($line->getTaxPercentage()));
                }
            }

            $nature = $line->getVatNature();
            if ($nature) {
                $datiBeniServizi->addChild('Natura', $nature->value);
            }

            $reference = $line->getReference();
            if ($reference) {
                $datiBeniServizi->addChild('RiferimentoNormativo', SimpleXmlExtended::sanitizeText($reference));
            }
        }
    }
}
