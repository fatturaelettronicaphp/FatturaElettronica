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
