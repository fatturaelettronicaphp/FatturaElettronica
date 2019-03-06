<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Enums\DocumentType;
use Weble\FatturaElettronica\Enums\TransmissionFormat;
use DateTime;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class DigitalDocumentInstance implements ArrayableInterface, DigitalDocumentInstanceInterface
{
    use Arrayable;

    /** @var \Weble\FatturaElettronica\Enums\DocumentType */
    protected $documentType;

    /** @var string */
    protected $documentNumber;

    /** @var float */
    protected $amount;

    /** @var float */
    protected $amountTax;

    /** @var float */
    protected $documentTotal;

    /** @var DateTime */
    protected $documentDate;

    public function getDocumentDate (): DateTime
    {
        return $this->documentDate;
    }

    public function setDocumentDate ($documentDate, $format = null): DigitalDocumentInstanceInterface
    {
        if (!$documentDate instanceof DateTime) {
            if ($format) {
                $documentDate = DateTime::createFromFormat($format, $documentDate);
            } else {
                $documentDate = new DateTime();
            }
        }

        $this->documentDate = $documentDate;
        return $this;
    }

    public function getDocumentType (): \Weble\FatturaElettronica\Enums\DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType ($documentType): DigitalDocumentInstanceInterface
    {
        if (!$documentType instanceof DocumentType) {
            $documentType = DocumentType::from($documentType);
        }

        $this->documentType = $documentType;
        return $this;
    }

    public function getDocumentNumber (): string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber (string $documentNumber): DigitalDocumentInstanceInterface
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    public function getAmount (): float
    {
        return $this->amount;
    }

    public function setAmount (float $amount): DigitalDocumentInstanceInterface
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmountTax (): float
    {
        return $this->amountTax;
    }

    public function setAmountTax (float $amountTax): DigitalDocumentInstanceInterface
    {
        $this->amountTax = $amountTax;
        return $this;
    }

    public function getDocumentTotal (): float
    {
        return $this->documentTotal;
    }

    public function setDocumentTotal (float $documentTotal): DigitalDocumentInstanceInterface
    {
        $this->documentTotal = $documentTotal;
        return $this;
    }
}
