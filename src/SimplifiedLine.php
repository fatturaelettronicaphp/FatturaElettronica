<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\SimplifiedLineInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class SimplifiedLine implements ArrayableInterface, SimplifiedLineInterface
{
    use Arrayable;

    /** @var string */
    protected $description;

    /** @var float */
    protected $total;

    /** @var float */
    protected $taxAmount;

    /** @var float */
    protected $taxPercentage;

    /** @var VatNature */
    protected $vatNature;

    /** @var string */
    protected $reference;

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Line
     */
    public function setDescription(?string $description): SimplifiedLineInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxAmount(): ?float
    {
        return $this->taxAmount;
    }

    /**
     * @param float $taxAmount
     *
     * @return Total
     */
    public function setTaxAmount(?float $taxAmount): SimplifiedLineInterface
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxPercentage(): ?float
    {
        return $this->taxPercentage;
    }

    /**
     * @param float $taxPercentage
     *
     * @return Total
     */
    public function setTaxPercentage(?float $taxPercentage): SimplifiedLineInterface
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @param float $total
     *
     * @return Total
     */
    public function setTotal(?float $total): SimplifiedLineInterface
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return VatNature
     */
    public function getVatNature(): ?VatNature
    {
        return $this->vatNature;
    }

    public function setVatNature($vatNature): SimplifiedLineInterface
    {
        if ($vatNature === null) {
            return $this;
        }

        if (! $vatNature instanceof VatNature) {
            $vatNature = VatNature::from($vatNature);
        }

        $this->vatNature = $vatNature;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return Total
     */
    public function setReference(?string $reference): SimplifiedLineInterface
    {
        $this->reference = $reference;

        return $this;
    }
}
