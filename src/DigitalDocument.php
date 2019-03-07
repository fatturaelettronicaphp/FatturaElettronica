<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class DigitalDocument implements ArrayableInterface, DigitalDocumentInterface
{
    use Arrayable;

    /** @var BillableInterface */
    protected $customer;

    /** @var BillableInterface */
    protected $supplier;

    /** @var TransmissionFormat */
    protected $transmissionFormat;

    /** @var string */
    protected $countryCode;

    /** @var string */
    protected $senderVatId;

    /** @var string */
    protected $sendingId;

    /** @var string */
    protected $customerSdiCode;

    /** @var string */
    protected $senderPhone;

    /** @var string */
    protected $senderEmail;

    /** @var string */
    protected $customerPec;

    /** @var \Weble\FatturaElettronica\DigitalDocumentInstance[] */
    protected $documentInstances;

    public function addDigitalDocumentInstance (DigitalDocumentInstance $instance): DigitalDocumentInterface
    {
        $this->documentInstances[] = $instance;
        return $this;
    }

    public function getDocumentInstances (): array
    {
        return $this->documentInstances;
    }

    public function getCountryCode (): string
    {
        return $this->countryCode;
    }

    public function setCountryCode (string $countryCode): DigitalDocumentInterface
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getCustomerSdiCode (): string
    {
        return $this->customerSdiCode;
    }

    public function setCustomerSdiCode (string $customerSdiCode): DigitalDocumentInterface
    {
        $this->customerSdiCode = $customerSdiCode;
        return $this;
    }

    public function getSenderVatId (): string
    {
        return $this->senderVatId;
    }

    public function setSenderVatId (string $senderVatId): DigitalDocumentInterface
    {
        $this->senderVatId = $senderVatId;
        return $this;
    }

    public function getSendingId (): string
    {
        return $this->sendingId;
    }

    public function setSendingId (string $sendingId): DigitalDocumentInterface
    {
        $this->sendingId = $sendingId;
        return $this;
    }

    public function getSenderPhone (): string
    {
        return $this->senderPhone;
    }

    public function setSenderPhone (string $senderPhone): DigitalDocumentInterface
    {
        $this->senderPhone = $senderPhone;
        return $this;
    }

    public function getSenderEmail (): string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail (string $senderEmail): DigitalDocumentInterface
    {
        $this->senderEmail = $senderEmail;
        return $this;
    }

    public function getCustomerPec (): string
    {
        return $this->customerPec;
    }

    public function setCustomerPec (string $customerPec): DigitalDocumentInterface
    {
        $this->customerPec = $customerPec;
        return $this;
    }

    public function getCustomer (): BillableInterface
    {
        return $this->customer;
    }

    public function setCustomer (BillableInterface $customer): DigitalDocumentInterface
    {
        $this->customer = $customer;
        return $this;
    }

    public function getSupplier (): BillableInterface
    {
        return $this->supplier;
    }

    public function setSupplier (BillableInterface $supplier): DigitalDocumentInterface
    {
        $this->supplier = $supplier;
        return $this;
    }

    public function getTransmissionFormat (): TransmissionFormat
    {
        return $this->transmissionFormat;
    }

    public function setTransmissionFormat ($transmissionFormat): DigitalDocumentInterface
    {
        if (!$transmissionFormat instanceof TransmissionFormat) {
            $transmissionFormat = TransmissionFormat::from($transmissionFormat);
        }

        $this->transmissionFormat = $transmissionFormat;

        return $this;
    }
}
