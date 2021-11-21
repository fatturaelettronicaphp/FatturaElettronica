<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use FatturaElettronicaPhp\FatturaElettronica\Enums\DeductionType;

interface DeductionInterface
{
    public function getType(): ?DeductionType;

    public function setType($deductionType): DeductionInterface;

    public function getPercentage(): ?float;

    public function setPercentage(?float $deductionPercentage): DeductionInterface;

    public function getAmount(): ?float;

    public function setAmount(?float $deductionAmount): DeductionInterface;

    public function getDescription(): ?string;

    public function setDescription(?string $deductionDescription): DeductionInterface;
}
