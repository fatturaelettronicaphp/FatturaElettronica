<?php

namespace Weble\FatturaElettronica\Validator\Body;


use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Validator\AbstractValidator;

abstract class AbstractBodyValidator extends AbstractValidator
{
    /** @var DigitalDocumentInstanceInterface */
    protected $body;

    public function validate (DigitalDocumentInstanceInterface $body): array
    {
        $this->body = $body;

        $this->performValidate();

        return $this->errors;
    }
}
