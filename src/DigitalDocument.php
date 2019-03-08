<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\BillablePersonInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\IntermediaryInterface;
use Weble\FatturaElettronica\Contracts\RepresentativeInterface;
use Weble\FatturaElettronica\Contracts\SupplierInterface;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class DigitalDocument implements ArrayableInterface, DigitalDocumentInterface
{
    use Arrayable;

    /** @var BillableInterface */
    protected $customer;

    /** @var SupplierInterface */
    protected $supplier;

    /** @var string */
    protected $emittingSubject;

    /** @var \Weble\FatturaElettronica\Contracts\BillablePersonInterface */
    protected $representative;

    /** @var \Weble\FatturaElettronica\Contracts\IntermediaryInterface */
    protected $intermediary;

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

    public function getEmittingSubject ()
    {
        return $this->emittingSubject;
    }

    public function setEmittingSubject ($emittingSubject): DigitalDocumentInterface
    {
        $this->emittingSubject = $emittingSubject;
        return $this;
    }

    public function getRepresentative (): ?BillablePersonInterface
    {
        return $this->representative;
    }

    public function setRepresentative (?BillablePersonInterface $representative): DigitalDocumentInterface
    {
        $this->representative = $representative;
        return $this;
    }

    public function getIntermediary (): ?IntermediaryInterface
    {
        return $this->intermediary;
    }

    public function setIntermediary (?IntermediaryInterface $intermediary): DigitalDocumentInterface
    {
        $this->intermediary = $intermediary;
        return $this;
    }

    public function addDigitalDocumentInstance (DigitalDocumentInstance $instance): DigitalDocumentInterface
    {
        $this->documentInstances[] = $instance;
        return $this;
    }

    public function getDocumentInstances (): array
    {
        return $this->documentInstances;
    }

    public function getCountryCode ()
    {
        return $this->countryCode;
    }

    public function setCountryCode ($countryCode): DigitalDocumentInterface
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getCustomerSdiCode ()
    {
        return $this->customerSdiCode;
    }

    public function setCustomerSdiCode ($customerSdiCode): DigitalDocumentInterface
    {
        $this->customerSdiCode = $customerSdiCode;
        return $this;
    }

    public function getSenderVatId ()
    {
        return $this->senderVatId;
    }

    public function setSenderVatId ($senderVatId): DigitalDocumentInterface
    {
        $this->senderVatId = $senderVatId;
        return $this;
    }

    public function getSendingId ()
    {
        return $this->sendingId;
    }

    public function setSendingId ($sendingId): DigitalDocumentInterface
    {
        $this->sendingId = $sendingId;
        return $this;
    }

    public function getSenderPhone ()
    {
        return $this->senderPhone;
    }

    public function setSenderPhone ($senderPhone): DigitalDocumentInterface
    {
        $this->senderPhone = $senderPhone;
        return $this;
    }

    public function getSenderEmail ()
    {
        return $this->senderEmail;
    }

    public function setSenderEmail ($senderEmail): DigitalDocumentInterface
    {
        $this->senderEmail = $senderEmail;
        return $this;
    }

    public function getCustomerPec ()
    {
        return $this->customerPec;
    }

    public function setCustomerPec ($customerPec): DigitalDocumentInterface
    {
        $this->customerPec = $customerPec;
        return $this;
    }

    public function getCustomer (): ?BillableInterface
    {
        return $this->customer;
    }

    public function setCustomer (BillableInterface $customer): DigitalDocumentInterface
    {
        $this->customer = $customer;
        return $this;
    }

    public function getSupplier (): ?SupplierInterface
    {
        return $this->supplier;
    }

    public function setSupplier (SupplierInterface $supplier): DigitalDocumentInterface
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
