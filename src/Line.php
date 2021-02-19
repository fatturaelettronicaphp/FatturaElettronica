<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\LineInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\OtherDataInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\ProductInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\CancelType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Line implements ArrayableInterface, LineInterface
{
    use Arrayable;

    /** @var int */
    protected $number;
    /** @var CancelType */
    protected $tipoCessazionePrestazione;
    /** @var ProductInterface[] */
    protected $products = [];
    /** @var string */
    protected $description;
    /** @var float */
    protected $quantity;
    /** @var string */
    protected $unit;
    /** @var DateTime */
    protected $startDate;
    /** @var DateTime */
    protected $endDate;
    /** @var float */
    protected $unitPrice;
    /** @var DiscountInterface[] */
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
    /** @var OtherDataInterface */
    protected $otherData = [];

    /**
     * @return int
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @param int $number
     *
     * @return Line
     */
    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getTipoCessazionePrestazione(): ?CancelType
    {
        return $this->tipoCessazionePrestazione;
    }

    public function setTipoCessazionePrestazione($tipoCessazionePrestazione): self
    {
        if ($tipoCessazionePrestazione === null) {
            return $this;
        }

        if (! $tipoCessazionePrestazione instanceof CancelType) {
            $tipoCessazionePrestazione = CancelType::from($tipoCessazionePrestazione);
        }

        $this->tipoCessazionePrestazione = $tipoCessazionePrestazione;

        return $this;
    }

    /**
     * @return ProductInterface[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param ProductInterface[] $products
     *
     * @return Line
     */
    public function addProduct(ProductInterface $product): self
    {
        $this->products[] = $product;

        return $this;
    }

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
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     *
     * @return Line
     */
    public function setQuantity(?float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnit(): ?string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     *
     * @return Line
     */
    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate($startDate, $format = null): self
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
     * @return DateTime
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate($endDate, $format = null): self
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
    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     *
     * @return Line
     */
    public function setUnitPrice(?float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    public function addDiscount(?DiscountInterface $discounts): self
    {
        $this->discounts[] = $discounts;

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
     * @return Line
     */
    public function setTotal(?float $total): self
    {
        $this->total = $total;

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
     * @return Line
     */
    public function setTaxPercentage(?float $taxPercentage): self
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeduction(): bool
    {
        return $this->deduction;
    }

    /**
     * @param bool $deduction
     *
     * @return Line
     */
    public function setDeduction($deduction): self
    {
        $this->deduction = (bool) $deduction;

        return $this;
    }

    /**
     * @return VatNature
     */
    public function getVatNature(): ?VatNature
    {
        return $this->vatNature;
    }

    public function setVatNature($vatNature): self
    {
        if ($vatNature === null) {
            return $this;
        }

        if (! $vatNature instanceof CancelType) {
            $vatNature = VatNature::from($vatNature);
        }

        $this->vatNature = $vatNature;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdministrativeContact(): ?string
    {
        return $this->administrativeContact;
    }

    /**
     * @param string $administrativeContact
     *
     * @return Line
     */
    public function setAdministrativeContact(?string $administrativeContact): self
    {
        $this->administrativeContact = $administrativeContact;

        return $this;
    }

    /**
     * @return OtherDataInterface
     */
    public function getOtherData(): array
    {
        return $this->otherData;
    }

    /**
     * @param OtherDataInterface $otherData
     *
     * @return Line
     */
    public function addOtherData(OtherDataInterface $otherData): self
    {
        $this->otherData[] = $otherData;

        return $this;
    }
}
