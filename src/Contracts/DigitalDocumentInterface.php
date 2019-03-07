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

    public function setCountryCode (string $countryCode): DigitalDocumentInterface;

    public function setSenderPhone (string $senderPhone): DigitalDocumentInterface;

    public function getSenderPhone (): string;

    public function setSenderEmail (string $senderEmail): DigitalDocumentInterface;

    public function setSendingId (string $sendingId): DigitalDocumentInterface;

    public function getCountryCode (): string;

    public function setSenderVatId (string $senderVatId): DigitalDocumentInterface;

    public function getSenderEmail (): string;

    public function getCustomerPec (): string;

    public function setCustomerPec (string $customerPec): DigitalDocumentInterface;

    public function getCustomerSdiCode (): string;

    public function setCustomerSdiCode (string $sdiCode): DigitalDocumentInterface;
}