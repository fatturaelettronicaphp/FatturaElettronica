<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class ShippingLabel implements ArrayableInterface
{
    use Arrayable;

    /** @var string */
    protected $documentNumber;
    /** @var DateTime */
    protected $documentDate;
    /** @var string[] */
    protected $lineNumberReferences = [];

    public function getLineNumberReference(): ?string
    {
        return $this->lineNumberReferences[0] ?? null;
    }

    public function getLineNumberReferences(): array
    {
        return $this->lineNumberReferences;
    }

    public function setLineNumberReference(?string $lineNumberReference)
    {
        $this->lineNumberReferences = [$lineNumberReference];

        return $this;
    }

    public function addLineNumberReference(?string $lineNumberReference)
    {
        $this->lineNumberReferences[] = $lineNumberReference;

        return $this;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(?string $documentNumber)
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    public function getDocumentDate(): ?DateTime
    {
        return $this->documentDate;
    }

    public function setDocumentDate($documentDate, $format = null)
    {
        if ($documentDate === null) {
            return $this;
        }

        if ($format !== null) {
            $this->documentDate = DateTime::createFromFormat($format, $documentDate);

            return $this;
        }

        if ($documentDate instanceof DateTime) {
            $this->documentDate = $documentDate;

            return $this;
        }

        $this->documentDate = new DateTime($documentDate);

        return $this;
    }
}
