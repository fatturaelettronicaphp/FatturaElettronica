<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\TotalInterface;
use Weble\FatturaElettronica\Enums\VatNature;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;
use Weble\FatturaElettronica\Enums\VatEligibility;

class Total implements ArrayableInterface, TotalInterface
{
    use Arrayable;

    /** @var float */
    protected $taxAmount;

    /** @var float */
    protected $taxPercentage;

    /** @var float */
    protected $total;

    /** @var VatNature */
    protected $vatNature;

    /** @var float */
    protected $otherExpenses;

    /** @var float */
    protected $rounding;

    /** @var VatEligibility */
    protected $taxType;

    /** @var string */
    protected $reference;

    /**
     * @return float
     */
    public function getTaxAmount (): ?float
    {
        return $this->taxAmount;
    }

    /**
     * @param float $taxAmount
     *
     * @return Total
     */
    public function setTaxAmount (?float $taxAmount): self
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxPercentage (): ?float
    {
        return $this->taxPercentage;
    }

    /**
     * @param float $taxPercentage
     *
     * @return Total
     */
    public function setTaxPercentage (?float $taxPercentage): self
    {
        $this->taxPercentage = $taxPercentage;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal (): ?float
    {
        return $this->total;
    }

    /**
     * @param float $total
     *
     * @return Total
     */
    public function setTotal (?float $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return \Weble\FatturaElettronica\Enums\VatNature
     */
    public function getVatNature (): ?VatNature
    {
        return $this->vatNature;
    }

    public function setVatNature ( $vatNature): self
    {
        if ($vatNature === null) {
            return $this;
        }

        if (!$vatNature instanceof VatNature) {
            $vatNature = VatNature::from($vatNature);
        }

        $this->vatNature = $vatNature;
        return $this;
    }

    /**
     * @return float
     */
    public function getOtherExpenses (): ?float
    {
        return $this->otherExpenses;
    }

    /**
     * @param float $otherExpenses
     *
     * @return Total
     */
    public function setOtherExpenses (?float $otherExpenses): self
    {
        $this->otherExpenses = $otherExpenses;
        return $this;
    }

    /**
     * @return float
     */
    public function getRounding (): ?float
    {
        return $this->rounding;
    }

    /**
     * @param float $rounding
     *
     * @return Total
     */
    public function setRounding (?float $rounding): self
    {
        $this->rounding = $rounding;
        return $this;
    }

    /**
     * @return \Weble\FatturaElettronica\Enums\VatEligibility
     */
    public function getTaxType (): ?VatEligibility
    {
        return $this->taxType;
    }

    public function setTaxType ( $taxType): self
    {
        if ($taxType === null) {
            return $this;
        }

        if (!$taxType instanceof VatEligibility) {
            $taxType = VatEligibility::from($taxType);
        }

        $this->taxType = $taxType;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference (): ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return Total
     */
    public function setReference (?string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }


}
