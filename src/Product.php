<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\ProductInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

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
