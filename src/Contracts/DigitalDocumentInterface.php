<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;
use FatturaElettronicaPhp\FatturaElettronica\Validator\DigitalDocumentValidator;
use SimpleXMLElement;

interface DigitalDocumentInterface
{
    public static function parseFrom($xml): DigitalDocumentInterface;

    public function serialize(): SimpleXMLElement;

    public function write(string $filePath): bool;

    public function generatedFilename(): string;

    public function isValid(): bool;

    public function validate(): DigitalDocumentValidator;

    public function addDigitalDocumentInstance(DigitalDocumentInstanceInterface $instance);

    /** @return DigitalDocumentInstanceInterface[] */
    public function getDocumentInstances(): array;

    public function getCustomer(): ?CustomerInterface;

    public function setCustomer(CustomerInterface $customer);

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

    public function isSimplified(): bool;

    public function setVersion(?string $version);

    public function getVersion();

    public function getEmittingSystem();

    public function setEmittingSystem(?string $system);
}
