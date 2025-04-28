<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

interface DigitalDocumentValidatorInterface
{
    public function isValid(): bool;

    public function errors(): array;

    public function validate(): DigitalDocumentValidatorInterface;
}
