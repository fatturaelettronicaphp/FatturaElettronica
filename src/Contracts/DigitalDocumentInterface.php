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
}