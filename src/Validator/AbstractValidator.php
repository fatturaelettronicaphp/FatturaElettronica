<?php

namespace Weble\FatturaElettronica\Validator;

use League\ISO3166\Exception\DomainException;
use League\ISO3166\Exception\InvalidArgumentException;
use League\ISO3166\ISO3166;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;

abstract class AbstractValidator
{
    protected $errors = [];

    public function __construct (array $errors)
    {
        $this->errors = $errors;
    }

    abstract protected function performValidate();

    protected function validateAddress(AddressInterface $address, string $rootElement ): self
    {
        if ($address->getStreet() === null) {
            $this->errors[$rootElement . '/Indirizzo'][] = 'required';
        }

        if ($address->getZip() === null) {
            $this->errors[$rootElement . '/CAP'][] = 'required';
        }

        if ($address->getCity() === null) {
            $this->errors[$rootElement . '/Comune'][] = 'required';
        }

        if ($address->getCountryCode() === null) {
            $this->errors[$rootElement . '/Nazione'][] = 'required';
        }

        $this->validateCountryCode($address->getCountryCode(), $rootElement . '/Nazione');

        return $this;
    }


    protected function validateCountryCode(?string $code, string $element, $required = true): self
    {
        if ($required) {
            if ($code === null) {
                $this->errors[$element][] = 'required';
                return $this;
            }
        }
        try {
            (new ISO3166())->alpha2($code);
        } catch (InvalidArgumentException $e) {
            $this->errors[ $element][] = $e->getMessage();
        } catch (DomainException $e) {
            $this->errors[ $element][] = $e->getMessage();
        }

        return $this;
    }
}
