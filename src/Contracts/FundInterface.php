<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use FatturaElettronicaPhp\FatturaElettronica\Enums\FundType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature;

interface FundInterface
{
    public function getType(): ?FundType;

    public function setType($fundType): FundInterface;

    public function getPercentage(): ?float;

    public function setPercentage(?float $fundPercentage): FundInterface;

    public function getAmount(): ?float;

    public function setAmount(?float $fundAmount): FundInterface;

    public function getSubtotal(): ?float;

    public function setSubtotal(?float $fundSubtotal): FundInterface;

    public function getTaxPercentage(): ?float;

    public function setTaxPercentage(?float $fundTaxPercentage): FundInterface;

    public function hasDeduction(): ?bool;

    public function setDeduction(?bool $fundDeduction): FundInterface;

    public function getVatNature(): ?VatNature;

    public function setVatNature($fundVatNature): FundInterface;

    public function getRepresentative(): ?string;

    public function setRepresentative(?string $fundRepresentative): FundInterface;
}
