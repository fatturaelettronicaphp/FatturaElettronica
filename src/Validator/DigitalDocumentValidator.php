<?php

namespace Weble\FatturaElettronica\Validator;

use League\ISO3166\Exception\DomainException;
use League\ISO3166\Exception\InvalidArgumentException;
use League\ISO3166\ISO3166;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;

class DigitalDocumentValidator
{
    /** @var DigitalDocumentInterface */
    protected $document;

    protected $errors = [];

    public function __construct(DigitalDocumentInterface $document)
    {
        $this->document = $document;
    }

    public function isValid(): bool
    {
        $this->performValidation();

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

        return $this;
    }

    protected function validateHeader(): self
    {
        if ($this->document->getCountryCode() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese'][] = 'required';
        }

        $this->validateCountryCode($this->document->getCountryCode(), '//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese');

        if ($this->document->getSenderVatId() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice'][] = 'required';
        }

        $length = strlen($this->document->getSenderVatId());
        if ($length > 28 || $length < 1) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice'][] = 'Length must be [1,28]';
        }

        if ($this->document->getSendingId() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio'][] = 'required';
        }

        $length = strlen($this->document->getSendingId());
        if ($length > 10 || $length < 1) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio'][] = 'Length must be [1,10]';
        }

        if ($this->document->getTransmissionFormat() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione'][] = 'required';
        }

        if ($this->document->getCustomerSdiCode() === null) {
            $this->errors['//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario'][] = 'required';
        }

        $this->validateSupplier();

        return $this;
    }

    protected function validateSupplier(): self
    {
        $supplier = $this->document->getSupplier();
        if ($supplier === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore'][] = 'required';
            return $this;
        }

        if ($supplier->getCountryCode() === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese'][] = 'required';
        }

        $this->validateCountryCode($supplier->getCountryCode(), '//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese');

        if ($supplier->getVatNumber() === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'required';
        }

        $length = strlen($supplier->getVatNumber());
        if ($length > 28 || $length < 1) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice'][] = 'Length must be [1,28]';
        }

        if ($supplier->getOrganization() === null && $supplier->getSurname() === null && $supplier->getName() === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica'][] = 'required';
        }

        if ($supplier->getTaxRegime() === null) {
            $this->errors['//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RegimeFiscale'][] = 'required';
        }

        $this
            ->validateAddress($supplier->getAddress(), '//FatturaElettronicaHeader/CedentePrestatore/Sede');

        if ($supplier->getForeignFixedAddress()) {
            $this->validateAddress($supplier->getForeignFixedAddress(), '//FatturaElettronicaHeader/CedentePrestatore/StabileOrganizzazione');
        }

        return $this;
    }

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


    protected function validateCountryCode(string $code, string $element): self
    {
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
