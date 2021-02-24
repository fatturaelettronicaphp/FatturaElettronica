<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;

interface SimplifiedLineInterface
{
    /**
     * @return string
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(?string $description): self;

    /**
     * @return float
     */
    public function getTaxAmount(): ?float;

    /**
     * @param float $taxAmount
     *
     * @return self
     */
    public function setTaxAmount(?float $taxAmount): self;

    /**
     * @return float
     */
    public function getTaxPercentage(): ?float;

    /**
     * @param float $taxPercentage
     *
     * @return self
     */
    public function setTaxPercentage(?float $taxPercentage): self;

    /**
     * @return float
     */
    public function getTotal(): ?float;

    /**
     * @param float $total
     *
     * @return self
     */
    public function setTotal(?float $total): self;

    /**
     * @return VatNature
     */
    public function getVatNature(): ?VatNature;

    public function setVatNature($vatNature): self;

    /**
     * @return string
     */
    public function getReference(): ?string;

    /**
     * @param string $reference
     *
     * @return self
     */
    public function setReference(?string $reference): self;
}
