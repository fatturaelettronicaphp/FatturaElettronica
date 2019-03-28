<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use FatturaElettronicaPhp\FatturaElettronica\Enums\VatEligibility;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;
use FatturaElettronicaPhp\FatturaElettronica\Total;

interface TotalInterface
{
    /**
     * @return float
     */
    public function getTaxAmount (): ?float;

    /**
     * @param float $taxAmount
     *
     * @return Total
     */
    public function setTaxAmount (?float $taxAmount);

    /**
     * @return float
     */
    public function getTaxPercentage (): ?float;

    /**
     * @param float $taxPercentage
     *
     * @return Total
     */
    public function setTaxPercentage (?float $taxPercentage);

    /**
     * @return float
     */
    public function getTotal (): ?float;

    /**
     * @param float $total
     *
     * @return Total
     */
    public function setTotal (?float $total);

    /**
     * @return \FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature
     */
    public function getVatNature (): ?VatNature;

    /**
     * @param \FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature $vatNature
     *
     * @return Total
     */
    public function setVatNature ( $vatNature);

    /**
     * @return float
     */
    public function getOtherExpenses (): ?float;

    /**
     * @param float $otherExpenses
     *
     * @return Total
     */
    public function setOtherExpenses (?float $otherExpenses);

    /**
     * @return float
     */
    public function getRounding (): ?float;

    /**
     * @param float $rounding
     *
     * @return Total
     */
    public function setRounding (?float $rounding);

    /**
     * @return \FatturaElettronicaPhp\FatturaElettronica\Enums\VatEligibility
     */
    public function getTaxType (): ?VatEligibility;

    /**
     * @param \FatturaElettronicaPhp\FatturaElettronica\Enums\VatEligibility $taxType
     *
     * @return Total
     */
    public function setTaxType ($taxType);

    /**
     * @return string
     */
    public function getReference (): ?string;

    /**
     * @param string $reference
     *
     * @return Total
     */
    public function setReference (?string $reference);
}