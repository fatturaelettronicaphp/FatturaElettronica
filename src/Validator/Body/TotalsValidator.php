<?php

namespace Weble\FatturaElettronica\Validator\Body;

use Alcohol\ISO4217;
use DomainException;
use Weble\FatturaElettronica\Contracts\LineInterface;
use Weble\FatturaElettronica\Contracts\TotalInterface;

class TotalsValidator extends AbstractBodyValidator
{
    protected function performValidate(): array
    {
        $totals = $this->body->getTotals();
        if (count($totals) <= 0) {
            $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DatiRiepilogo'][] = 'required';
            return $this->errors;
        }

        /** @var TotalInterface $total */
        foreach ($totals as $total) {

            if ($total->getTaxPercentage() === null) {
                $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DatiRiepilogo/AliquotaIVA'][] = 'required';
            }

            if ($total->getTotal() === null) {
                $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DatiRiepilogo/ImponibileImporto'][] = 'required';
            }

            if ($total->getTaxAmount() === null) {
                $this->errors['//FatturaElettronicaBody/DatiBeniServizi/DatiRiepilogo/Imposta'][] = 'required';
            }
        }

        return $this->errors;
    }
}
