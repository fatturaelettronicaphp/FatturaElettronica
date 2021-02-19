<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\FundInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\FundType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Fund implements ArrayableInterface, FundInterface
{
    use Arrayable;

    /** @var FundType */
    protected $type;

    /** @var float */
    protected $percentage;

    /** @var float */
    protected $amount;

    /** @var float */
    protected $subtotal;

    /** @var float */
    protected $taxPercentage;

    /** @var bool */
    protected $deduction;

    /** @var VatNature */
    protected $vatNature;

    /** @var string */
    protected $representative;

    public function getType(): ?FundType
    {
        return $this->type;
    }

    public function setType($type): FundInterface
    {
        if ($type === null) {
            return $this;
        }

        if (! $type instanceof FundType) {
            $type = FundType::from($type);
        }

        $this->type = $type;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage): FundInterface
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): FundInterface
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(?float $subtotal): FundInterface
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getTaxPercentage(): ?float
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(?float $taxPercentage): FundInterface
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    public function hasDeduction(): ?bool
    {
        return $this->deduction;
    }

    public function setDeduction(?bool $deduction): FundInterface
    {
        $this->deduction = $deduction;

        return $this;
    }

    public function getVatNature(): ?VatNature
    {
        return $this->vatNature;
    }

    public function setVatNature($vatNature): FundInterface
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

    public function getRepresentative(): ?string
    {
        return $this->representative;
    }

    public function setRepresentative(?string $representative): FundInterface
    {
        $this->representative = $representative;

        return $this;
    }
}
