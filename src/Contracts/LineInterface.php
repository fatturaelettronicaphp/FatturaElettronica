<?php

namespace Weble\FatturaElettronica\Contracts;

use Weble\FatturaElettronica\Line;

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
     * @return \Weble\FatturaElettronica\Enums\TipoCessazionePrestazione
     */
    public function getTipoCessazionePrestazione (): ?\Weble\FatturaElettronica\Enums\TipoCessazionePrestazione;

    public function setTipoCessazionePrestazione ($tipoCessazionePrestazione);

    /**
     * @return \Weble\FatturaElettronica\Contracts\ProductInterface[]
     */
    public function getProducts (): array;

    /**
     * @param \Weble\FatturaElettronica\Contracts\ProductInterface[] $products
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
     * @return \DateTime
     */
    public function getStartDate (): ?\DateTime;

    /**
     * @param \DateTime $startDate
     *
     * @return Line
     */
    public function setStartDate (\DateTime $startDate);

    /**
     * @return \DateTime
     */
    public function getEndDate (): ?\DateTime;

    /**
     * @param \DateTime $endDate
     *
     * @return Line
     */
    public function setEndDate (\DateTime $endDate);

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

    public function addDiscount (?\Weble\FatturaElettronica\Contracts\DiscountInterface $discounts);

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
    public function setDeduction (bool $deduction);

    /**
     * @return \Weble\FatturaElettronica\Enums\VatNature
     */
    public function getVatNature (): ?\Weble\FatturaElettronica\Enums\VatNature;

    /**
     * @param \Weble\FatturaElettronica\Enums\VatNature $vatNature
     *
     * @return Line
     */
    public function setVatNature (?\Weble\FatturaElettronica\Enums\VatNature $vatNature);

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
     * @return \Weble\FatturaElettronica\Contracts\OtherDataInterface
     */
    public function getOtherData (): array;

    /**
     * @param \Weble\FatturaElettronica\Contracts\OtherDataInterface $otherData
     *
     * @return Line
     */
    public function addOtherData (\Weble\FatturaElettronica\Contracts\OtherDataInterface $otherData);
}