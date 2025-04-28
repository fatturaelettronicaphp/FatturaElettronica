<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Validator\Check;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillablePersonInterface;

trait ChecksForValidFiscalCodes
{
    protected function hasVatIdOrFiscaleCode(BillablePersonInterface $billable): bool
    {
        $vatId = trim($billable->getVatNumber() ?? '');
        $fiscalCode = trim($billable->getFiscalCode() ?? '');

        return strlen($vatId) > 0 || strlen($fiscalCode) > 0;
    }
}
