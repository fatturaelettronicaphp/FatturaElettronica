<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\ProductInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DeductionType;
use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Enums\FundType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DiscountType;
use FatturaElettronicaPhp\FatturaElettronica\Fund;

class Product implements ArrayableInterface, ProductInterface
{
    use Arrayable;

    /** @var string */
    protected $codeType;
    /** @var string */
    protected $code;

    /**
     * @return string
     */
    public function getCodeType (): ?string
    {
        return $this->codeType;
    }

    /**
     * @param string $codeType
     *
     * @return Product
     */
    public function setCodeType (?string $codeType): Product
    {
        $this->codeType = $codeType;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode (): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Product
     */
    public function setCode (?string $code): Product
    {
        $this->code = $code;
        return $this;
    }



}
