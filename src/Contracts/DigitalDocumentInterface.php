<?php

namespace Weble\FatturaElettronica\Contracts;

use DateTime;
use Weble\FatturaElettronica\DigitalDocument;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use Weble\FatturaElettronica\Enums\TransmissionFormat;

interface DigitalDocumentInterface
{
    public function addDigitalDocumentInstance (DigitalDocumentInstance $instance): DigitalDocumentInterface;

    public function getDocumentInstances (): array;

    public function getCustomer (): BillableInterface;

    public function setCustomer (BillableInterface $customer): DigitalDocumentInterface;

    public function getSupplier (): BillableInterface;

    public function setSupplier (BillableInterface $supplier): DigitalDocumentInterface;

    public function getTransmissionFormat (): TransmissionFormat;

    public function setTransmissionFormat ($transmissionFormat): DigitalDocumentInterface;

    public function getSenderVatId (): string;

    public function getSendingId (): string;

    public function setCountryCode ($countryCode): DigitalDocumentInterface;

    public function setSenderPhone ($senderPhone): DigitalDocumentInterface;

    public function getSenderPhone (): string;

    public function setSenderEmail ($senderEmail): DigitalDocumentInterface;

    public function setSendingId ($sendingId): DigitalDocumentInterface;

    public function getCountryCode (): string;

    public function setSenderVatId ($senderVatId): DigitalDocumentInterface;

    public function getSenderEmail (): string;

    public function getCustomerPec (): string;

    public function setCustomerPec ($customerPec): DigitalDocumentInterface;

    public function getCustomerSdiCode (): string;

    public function setCustomerSdiCode ($sdiCode): DigitalDocumentInterface;
}