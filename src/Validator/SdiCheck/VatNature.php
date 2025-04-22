<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Validator\SdiCheck;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentValidatorInterface;
use FatturaElettronicaPhp\FatturaElettronica\Validator\BasicDigitalDocumentValidator;

class VatNature extends BasicDigitalDocumentValidator
{
    public function validate(): DigitalDocumentValidatorInterface
    {
        $instances = $this->document->getDocumentInstances();

        foreach ($instances as $documentIndex => $instance) {
            foreach ($instance->getLines() as $lineIndex => $line) {
                $key = implode(".", [$documentIndex, $lineIndex, 'Natura']);
                if ($line->getTaxPercentage() <= 0 && $line->getVatNature() === null) {
                    $this->errors[$key] = "Errore 00400: 2.2.1.14 <Natura> non presente a fronte di 2.2.1.12 <AliquotaIVA> pari a zero";

                    continue;
                }

                if ($line->getTaxPercentage() > 0 && $line->getVatNature() !== null) {
                    $this->errors[$key] = "Errore 00401: 2.2.1.14 <Natura> presente a fronte di 2.2.1.12 <AliquotaIVA> diversa da zero";
                }
            }
        }

        return $this;
    }
}
