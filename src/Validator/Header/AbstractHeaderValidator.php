<?php

namespace Weble\FatturaElettronica\Validator\Header;


use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Validator\AbstractValidator;

abstract class AbstractHeaderValidator extends AbstractValidator
{
    /** @var DigitalDocumentInterface */
    protected $document;

    public function validate (DigitalDocumentInterface $document): array
    {
        $this->document = $document;

        $this->performValidate();

        return $this->errors;
    }
}
