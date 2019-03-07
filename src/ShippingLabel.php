<?php

namespace Weble\FatturaElettronica;

use DateTime;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class ShippingLabel implements ArrayableInterface
{
    use Arrayable;

    /** @var string */
    protected $documentNumber;
    /** @var DateTime */
    protected $documentDate;
    /** @var string */
    protected $lineNumberReference;

    public function getLineNumberReference (): ?string
    {
        return $this->lineNumberReference;
    }

    public function setLineNumberReference (?string $lineNumberReference)
    {
        $this->lineNumberReference = $lineNumberReference;
        return $this;
    }

    public function getDocumentNumber (): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber (?string $documentNumber)
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    public function getDocumentDate (): ?DateTime
    {
        return $this->documentDate;
    }

    public function setDocumentDate ($documentDate, $format = null)
    {
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
