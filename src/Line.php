<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\LineInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\CancelType;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\ProductInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\OtherDataInterface;

class Line implements ArrayableInterface, LineInterface
{
    use Arrayable;

    /** @var int */
    protected $number;
    /** @var CancelType */
    protected $tipoCessazionePrestazione;
    /** @var  ProductInterface[] */
    protected $products = [];
    /** @var string */
    protected $description;
    /** @var float */
    protected $quantity;
    /** @var string */
    protected $unit;
    /** @var \DateTime */
    protected $startDate;
    /** @var \DateTime */
    protected $endDate;
    /** @var float */
    protected $unitPrice;
    /** @var DiscountInterface[]  */
    protected $discounts = [];
    /** @var float */
    protected $total;
    /** @var float */
    protected $taxPercentage;
    /** @var bool */
    protected $deduction = false;
    /** @var VatNature */
    protected $vatNature;
    /** @var string */
    protected $administrativeContact;
    /** @var OtherDataInterface  */
    protected $otherData = [];

    /**
     * @return int
     */
    public function getNumber (): ?int
    {
        return $this->number;
    }

    /**
     * @param int $number
     *
     * @return Line
     */
    public function setNumber (?int $number): Line
    {
        $this->number = $number;
        return $this;
    }


    public function getTipoCessazionePrestazione (): ?CancelType
    {
        return $this->tipoCessazionePrestazione;
    }


    public function setTipoCessazionePrestazione ($tipoCessazionePrestazione): Line
    {
        if ($tipoCessazionePrestazione === null) {
            return $this;
        }

        if (!$tipoCessazionePrestazione instanceof CancelType) {
            $tipoCessazionePrestazione = CancelType::from($tipoCessazionePrestazione);
        }

        $this->tipoCessazionePrestazione = $tipoCessazionePrestazione;
        return $this;
    }

    /**
     * @return \FatturaElettronicaPhp\FatturaElettronica\Contracts\ProductInterface[]
     */
    public function getProducts (): array
    {
        return $this->products;
    }

    /**
     * @param \FatturaElettronicaPhp\FatturaElettronica\Contracts\ProductInterface[] $products
     *
     * @return Line
     */
    public function addProduct (ProductInterface $product): Line
    {
        $this->products[] = $product;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription (): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Line
     */
    public function setDescription (?string $description): Line
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity (): ?float
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     *
     * @return Line
     */
    public function setQuantity (?float $quantity): Line
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit (): ?string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     *
     * @return Line
     */
    public function setUnit (?string $unit): Line
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate (): ?DateTime
    {
        return $this->startDate;
    }


    public function setStartDate ($startDate, $format = null): Line
    {
        if ($startDate === null) {
            return $this;
        }

        if ($format !== null) {
            $this->startDate = DateTime::createFromFormat($format, $startDate);
            return $this;
        }

        if ($startDate instanceof DateTime) {
            $this->startDate = $startDate;
            return $this;
        }

        $this->startDate = new DateTime($startDate);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate (): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate ($endDate, $format = null): Line
    {
        if ($endDate === null) {
            return $this;
        }

        if ($format !== null) {
            $this->endDate = DateTime::createFromFormat($format, $endDate);
            return $this;
        }

        if ($endDate instanceof DateTime) {
            $this->endDate = $endDate;
            return $this;
        }

        $this->endDate = new DateTime($endDate);
        return $this;
    }

    /**
     * @return float
     */
    public function getUnitPrice (): ?float
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     *
     * @return Line
     */
    public function setUnitPrice (?float $unitPrice): Line
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }


    public function getDiscounts (): array
    {
        return $this->discounts;
    }


    public function addDiscount (?\FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface $discounts): Line
    {
        $this->discounts[] = $discounts;
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
     * @return Line
     */
    public function setTotal (?float $total): Line
    {
        $this->total = $total;
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
     * @return Line
     */
    public function setTaxPercentage (?float $taxPercentage): Line
    {
        $this->taxPercentage = $taxPercentage;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeduction (): bool
    {
        return $this->deduction;
    }

    /**
     * @param bool $deduction
     *
     * @return Line
     */
    public function setDeduction ($deduction): Line
    {
        $this->deduction = (bool) $deduction;
        return $this;
    }

    /**
     * @return VatNature
     */
    public function getVatNature (): ?VatNature
    {
        return $this->vatNature;
    }

    public function setVatNature ($vatNature): Line
    {
        if ($vatNature === null) {
            return $this;
        }

        if (!$vatNature instanceof CancelType) {
            $vatNature = VatNature::from($vatNature);
        }

        $this->vatNature = $vatNature;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdministrativeContact (): ?string
    {
        return $this->administrativeContact;
    }

    /**
     * @param string $administrativeContact
     *
     * @return Line
     */
    public function setAdministrativeContact (?string $administrativeContact): Line
    {
        $this->administrativeContact = $administrativeContact;
        return $this;
    }

    /**
     * @return \FatturaElettronicaPhp\FatturaElettronica\Contracts\OtherDataInterface
     */
    public function getOtherData (): array
    {
        return $this->otherData;
    }

    /**
     * @param \FatturaElettronicaPhp\FatturaElettronica\Contracts\OtherDataInterface $otherData
     *
     * @return Line
     */
    public function addOtherData (\FatturaElettronicaPhp\FatturaElettronica\Contracts\OtherDataInterface $otherData): Line
    {
        $this->otherData[] = $otherData;
        return $this;
    }



}
