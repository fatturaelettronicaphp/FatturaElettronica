<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Enums\CancelType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Line;

interface LineInterface
{
    /**
     * @return int
     */
    public function getNumber (): ?int;

    /**
     * @param int $number
     *
     * @return Line
     */
    public function setNumber (?int $number);

    /**
     * @return CancelType
     */
    public function getTipoCessazionePrestazione (): ?CancelType;

    public function setTipoCessazionePrestazione ($tipoCessazionePrestazione);

    /**
     * @return ProductInterface[]
     */
    public function getProducts (): array;

    /**
     * @param ProductInterface[] $products
     *
     * @return Line
     */
    public function addProduct (ProductInterface $product);

    /**
     * @return string
     */
    public function getDescription (): ?string;

    /**
     * @param string $description
     *
     * @return Line
     */
    public function setDescription (?string $description);

    /**
     * @return float
     */
    public function getQuantity (): ?float;

    /**
     * @param float $quantity
     *
     * @return Line
     */
    public function setQuantity (?float $quantity);

    /**
     * @return string
     */
    public function getUnit (): ?string;

    /**
     * @param string $unit
     *
     * @return Line
     */
    public function setUnit (?string $unit);

    /**
     * @return DateTime
     */
    public function getStartDate (): ?DateTime;


    public function setStartDate ($startDate, $format = null);

    /**
     * @return DateTime
     */
    public function getEndDate (): ?DateTime;

    public function setEndDate ($endDate, $format = null);

    /**
     * @return float
     */
    public function getUnitPrice (): ?float;

    /**
     * @param float $unitPrice
     *
     * @return Line
     */
    public function setUnitPrice (?float $unitPrice);

    public function getDiscounts (): array;

    public function addDiscount (?DiscountInterface $discounts);

    /**
     * @return float
     */
    public function getTotal (): ?float;

    /**
     * @param float $total
     *
     * @return Line
     */
    public function setTotal (?float $total);

    /**
     * @return float
     */
    public function getTaxPercentage (): ?float;

    /**
     * @param float $taxPercentage
     *
     * @return Line
     */
    public function setTaxPercentage (?float $taxPercentage);

    /**
     * @return bool
     */
    public function isDeduction (): bool;

    /**
     * @param bool $deduction
     *
     * @return Line
     */
    public function setDeduction ($deduction);

    /**
     * @return VatNature
     */
    public function getVatNature (): ?VatNature;

    /**
     * @param VatNature $vatNature
     *
     * @return Line
     */
    public function setVatNature ($vatNature);

    /**
     * @return string
     */
    public function getAdministrativeContact (): ?string;

    /**
     * @param string $administrativeContact
     *
     * @return Line
     */
    public function setAdministrativeContact (?string $administrativeContact);

    /**
     * @return OtherDataInterface
     */
    public function getOtherData (): array;

    /**
     * @param OtherDataInterface $otherData
     *
     * @return Line
     */
    public function addOtherData (OtherDataInterface $otherData);
}