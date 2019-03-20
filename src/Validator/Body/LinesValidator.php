<?php

namespace Weble\FatturaElettronica\Validator\Body;

use Alcohol\ISO4217;
use DomainException;
use Weble\FatturaElettronica\Contracts\LineInterface;

class LinesValidator extends AbstractBodyValidator
{
    protected function performValidate(): array
    {
        $lines = $this->body->getLines();
        if (count($lines) <= 0) {
            $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee'][] = 'required';
            return $this->errors;
        }

        /** @var LineInterface $line */
        foreach ($lines as $line) {
            if ($line->getNumber() === null) {
                $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee/NumeroLinea'][] = 'required';
            }

            if ($line->getDescription() === null) {
                $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee/Descrizione'][] = 'required';
            }

            if ($line->getUnitPrice() === null) {
                $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee/PrezzoUnitario'][] = 'required';
            }

            if ($line->getTotal() === null) {
                $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee/PrezzoTotale'][] = 'required';
            }

            if ($line->getTaxPercentage() === null) {
                $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee/AliquotaIVA'][] = 'required';
            }
        }

        return $this->errors;
    }
}
