<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DeductionInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DeductionType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DiscountType;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Deduction implements ArrayableInterface, DeductionInterface
{
    use Arrayable;

    /** @var DeductionType */
    protected $type;

    /** @var float */
    protected $percentage;

    /** @var float */
    protected $amount;

    /** @var string */
    protected $description;

    public function getType(): ?DeductionType
    {
        return $this->type;
    }

    public function setType($type): DeductionInterface
    {
        if ($type === null) {
            return $this;
        }

        if (! $type instanceof DeductionType) {
            $type = new DeductionType($type);
        }

        $this->type = $type;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage): DeductionInterface
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): DeductionInterface
    {
        $this->amount = $amount;

        return $this;
    }

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): DeductionInterface
	{
		$this->description = $description;

		return $this;
	}
}
