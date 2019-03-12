<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DiscountInterface;
use Weble\FatturaElettronica\Contracts\ProductInterface;
use Weble\FatturaElettronica\Enums\DocumentType;
use Weble\FatturaElettronica\Enums\DeductionType;
use DateTime;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;
use Weble\FatturaElettronica\Enums\VatNature;
use Weble\FatturaElettronica\Enums\FundType;
use Weble\FatturaElettronica\Enums\DiscountType;
use Weble\FatturaElettronica\Fund;

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
