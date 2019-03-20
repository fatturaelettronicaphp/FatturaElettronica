<?php

namespace Weble\FatturaElettronica\Validator;

use League\ISO3166\Exception\DomainException;
use League\ISO3166\Exception\InvalidArgumentException;
use League\ISO3166\ISO3166;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Utilities\Pipeline;
use Weble\FatturaElettronica\Validator\Body\DeductionValidator;
use Weble\FatturaElettronica\Validator\Body\GeneralDataValidator;
use Weble\FatturaElettronica\Validator\Body\LinesValidator;
use Weble\FatturaElettronica\Validator\Body\TotalsValidator;
use Weble\FatturaElettronica\Validator\Header\CustomerValidator;
use Weble\FatturaElettronica\Validator\Header\IntermediaryValidator;
use Weble\FatturaElettronica\Validator\Header\RepresentativeValidator;
use Weble\FatturaElettronica\Validator\Header\SupplierValidator;
use Weble\FatturaElettronica\Validator\Header\TransmissionDataValidator;

class DigitalDocumentValidator
{
    /** @var DigitalDocumentInterface */
    protected $document;

    protected $errors = [];

    public function __construct(DigitalDocumentInterface $document)
    {
        $this->document = $document;
        $this->performValidation();
    }

    public function isValid(): bool
    {
        return count($this->errors) <= 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    protected function performValidation(): self
    {
        $this->errors = [];

        $this->validateHeader();
        foreach ($this->document->getDocumentInstances() as $body) {
            $this->validateBody($body);
        }

        return $this;
    }

    protected function validateHeader(): self
    {
        $pipeline = new Pipeline();

        $this->errors = $pipeline
            ->send($this->errors)
            ->with($this->document)
            ->usingMethod('validate')
            ->through([
                TransmissionDataValidator::class,
                SupplierValidator::class,
                RepresentativeValidator::class,
                CustomerValidator::class,
                IntermediaryValidator::class
            ])
            ->thenReturn();

        return $this;
    }

    protected function validateBody(DigitalDocumentInstanceInterface $body): self
    {
        $pipeline = new Pipeline();

        $this->errors = $pipeline
            ->send($this->errors)
            ->with($body)
            ->usingMethod('validate')
            ->through([
                GeneralDataValidator::class,
                DeductionValidator::class,
                LinesValidator::class,
                TotalsValidator::class
            ])
            ->thenReturn();

        return $this;
    }
}
