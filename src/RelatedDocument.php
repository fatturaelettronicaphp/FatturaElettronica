<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\RelatedDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;


class RelatedDocument extends ShippingLabel implements ArrayableInterface, RelatedDocumentInterface
{
    use Arrayable;

    /** @var string */
    protected $lineNumber;
    /** @var string */
    protected $orderCode;
    /** @var string */
    protected $cupCode;
    /** @var string */
    protected $cigCode;


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
