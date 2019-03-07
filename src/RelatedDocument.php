<?php

namespace Weble\FatturaElettronica;

use DateTime;
use Weble\FatturaElettronica\Contracts\RelatedDocumentInterface;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;


class RelatedDocument implements ArrayableInterface, RelatedDocumentInterface
{
    use Arrayable;

    /** @var string */
    protected $lineNumberReference;
    /** @var string */
    protected $documentNumber;
    /** @var DateTime */
    protected $documentDate;
    /** @var string */
    protected $lineNumber;
    /** @var string */
    protected $orderCode;
    /** @var string */
    protected $cupCode;
    /** @var string */
    protected $cigCode;

    public function getLineNumberReference (): ?string
    {
        return $this->lineNumberReference;
    }

    public function setLineNumberReference (?string $lineNumberReference): RelatedDocument
    {
        $this->lineNumberReference = $lineNumberReference;
        return $this;
    }

    public function getDocumentNumber (): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber (?string $documentNumber): RelatedDocument
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    public function getDocumentDate (): ?DateTime
    {
        return $this->documentDate;
    }

    public function setDocumentDate ($documentDate, $format = null): RelatedDocument
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

    public function getLineNumber (): ?string
    {
        return $this->lineNumber;
    }

    public function setLineNumber (?string $lineNumber): RelatedDocument
    {
        $this->lineNumber = $lineNumber;
        return $this;
    }

    public function getOrderCode (): ?string
    {
        return $this->orderCode;
    }

    public function setOrderCode (?string $orderCode): RelatedDocument
    {
        $this->orderCode = $orderCode;
        return $this;
    }

    public function getCupCode (): ?string
    {
        return $this->cupCode;
    }

    public function setCupCode (?string $cupCode): RelatedDocument
    {
        $this->cupCode = $cupCode;
        return $this;
    }

    public function getCigCode (): ?string
    {
        return $this->cigCode;
    }

    public function setCigCode (?string $cigCode): RelatedDocument
    {
        $this->cigCode = $cigCode;
        return $this;
    }


}
