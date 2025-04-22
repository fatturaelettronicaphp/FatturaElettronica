<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Validator;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentValidatorInterface;
use FatturaElettronicaPhp\FatturaElettronica\Validator\SdiCheck\VatNature;

class SdiValidator extends MultipleDigitalDocumentValidator implements DigitalDocumentValidatorInterface
{
    protected const SDI_CHECKS = [
        VatNature::class,
    ];

    public function __construct(DigitalDocumentInterface $document)
    {
        parent::__construct($document);

        $this->withValidators(self::SDI_CHECKS);
    }
}
