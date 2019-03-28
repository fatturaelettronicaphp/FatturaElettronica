<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DeductionType;
use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Enums\FundType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DiscountType;
use FatturaElettronicaPhp\FatturaElettronica\Fund;

class Discount implements ArrayableInterface, DiscountInterface
{
    use Arrayable;

    /** @var DiscountType */
    protected $type;

    /** @var float */
    protected $percentage;

    /** @var float */
    protected $amount;

    public function getType (): ?DiscountType
    {
        return $this->type;
    }

    public function setType ($type): DiscountInterface
    {
        if ($type === null) {
            return $this;
        }

        if (!$type instanceof DiscountType) {
            $type = DiscountType::from($type);
        }

        $this->type = $type;
        return $this;
    }

    public function getPercentage (): ?float
    {
        return $this->percentage;
    }

    public function setPercentage (?float $percentage): DiscountInterface
    {
        $this->percentage = $percentage;
        return $this;
    }

    public function getAmount (): ?float
    {
        return $this->amount;
    }

    public function setAmount (?float $amount): DiscountInterface
    {
        $this->amount = $amount;
        return $this;
    }
}
