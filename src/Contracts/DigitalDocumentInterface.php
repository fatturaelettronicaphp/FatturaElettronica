<?php

namespace Weble\FatturaElettronica\Contracts;

use DateTime;
use SimpleXMLElement;
use Weble\FatturaElettronica\DigitalDocument;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use Weble\FatturaElettronica\Parser\DigitalDocumentParser;
use Weble\FatturaElettronica\Writer\DigitalDocumentWriter;

interface DigitalDocumentInterface
{
    public static function parseFrom($xml): DigitalDocumentInterface;

    public function serialize(): SimpleXMLElement;

    public function write(string $filePath): bool;

    public function addDigitalDocumentInstance(DigitalDocumentInstanceInterface $instance);

    public function getDocumentInstances(): array;

    public function getCustomer(): ?BillableInterface;

    public function setCustomer(BillableInterface $customer);

    public function getSupplier(): ?SupplierInterface;

    public function setSupplier(SupplierInterface $supplier);

    public function getTransmissionFormat(): ?TransmissionFormat;

    public function setTransmissionFormat($transmissionFormat);

    public function getSenderVatId();

    public function getSendingId();

    public function setCountryCode($countryCode);

    public function setSenderPhone($senderPhone);

    public function getSenderPhone();

    public function setSenderEmail($senderEmail);

    public function setSendingId($sendingId);

    public function getCountryCode();

    public function setSenderVatId($senderVatId);

    public function getSenderEmail();

    public function getCustomerPec();

    public function setCustomerPec($customerPec);

    public function getCustomerSdiCode();

    public function setCustomerSdiCode($sdiCode);

    public function setEmittingSubject($emittingSubject);

    public function getEmittingSubject();

    public function setIntermediary(?IntermediaryInterface $intermediary);

    public function getIntermediary(): ?IntermediaryInterface;

    public function setRepresentative(?BillablePersonInterface $representative);

    public function getRepresentative(): ?BillablePersonInterface;
}