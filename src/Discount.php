<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DiscountType;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Discount implements ArrayableInterface, DiscountInterface
{
    use Arrayable;

    /** @var DiscountType */
    protected $type;

    /** @var float */
    protected $percentage;

    /** @var float */
    protected $amount;

    public function getType(): ?DiscountType
    {
        return $this->type;
    }

    public function setType($type): DiscountInterface
    {
        if ($type === null) {
            return $this;
        }

        if (! $type instanceof DiscountType) {
            $type = DiscountType::from($type);
        }

        $this->type = $type;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage): DiscountInterface
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): DiscountInterface
    {
        $this->amount = $amount;

        return $this;
    }
}
