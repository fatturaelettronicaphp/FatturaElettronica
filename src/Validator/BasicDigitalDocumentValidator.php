<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Validator;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentValidatorInterface;

abstract class BasicDigitalDocumentValidator implements DigitalDocumentValidatorInterface
{
    protected array $errors = [];

    public function __construct(protected DigitalDocumentInterface $document)
    {
    }

    public function isValid(): bool
    {
        return count($this->errors) <= 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    abstract public function validate(): DigitalDocumentValidatorInterface;
}
