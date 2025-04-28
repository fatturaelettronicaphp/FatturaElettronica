<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Validator;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentValidatorInterface;

class DigitalDocumentValidator extends BasicDigitalDocumentValidator
{
    /**
     * @var class-string<DigitalDocumentValidatorInterface>[]
     */
    private array $validators = [];

    public function __construct(DigitalDocumentInterface $document)
    {
        parent::__construct($document);
    }

    /**
     * @param class-string<DigitalDocumentValidatorInterface>[] $validators
     * @return self
     */
    public function withValidators(array $validators): self
    {
        $this->validators = $validators;

        return $this;
    }

    public function validate(): DigitalDocumentValidatorInterface
    {
        foreach ($this->validators as $validatorClass) {
            $validator = (new $validatorClass($this->document))->validate();
            $this->errors = array_merge_recursive($this->errors, $validator->errors());
        }

        return $this;
    }
}
