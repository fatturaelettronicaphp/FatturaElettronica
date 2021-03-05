<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use FatturaElettronicaPhp\FatturaElettronica\Product;

interface ProductInterface
{
    /**
     * @return string
     */
    public function getCodeType(): ?string;

    /**
     * @param string $codeType
     *
     * @return Product
     */
    public function setCodeType(?string $codeType);

    /**
     * @return string
     */
    public function getCode(): ?string;

    /**
     * @param string $code
     *
     * @return Product
     */
    public function setCode(?string $code);
}
