<?php

namespace Weble\FatturaElettronica\Contracts;

use DateTime;
use Weble\FatturaElettronica\Enums\DeductionType;
use Weble\FatturaElettronica\Enums\DocumentType;
use Weble\FatturaElettronica\Fund;
use Weble\FatturaElettronica\RelatedDocument;

interface DigitalDocumentInstanceInterface
{
    public function getRounding (): ?float;

    public function setRounding (?float $rounding): DigitalDocumentInstanceInterface;

    public function getDescriptions (): array ;

    public function addDescription (string $description): DigitalDocumentInstanceInterface;

    public function getDocumentDate (): ?DateTime;

    public function setDocumentDate ($documentDate, $format = null): DigitalDocumentInstanceInterface;

    public function isArt73 (): bool;

    public function setArt73 (?bool $art73): DigitalDocumentInstanceInterface;

    public function getCurrency (): ?string;

    public function setCurrency (?string $currency): DigitalDocumentInstanceInterface;

    public function getDeductionType (): ?DeductionType;

    public function setDeductionType ($deductionType): DigitalDocumentInstanceInterface;

    public function getDeductionAmount (): ?float;

    public function setDeductionAmount (?float $deductionAmount): DigitalDocumentInstanceInterface;

    public function getDeductionPercentage (): ?float;

    public function setDeductionPercentage (?float $deductionPercentage): DigitalDocumentInstanceInterface;

    public function getDeductionDescription (): ?string;

    public function setDeductionDescription (?string $deductionDescription): DigitalDocumentInstanceInterface;

    public function getVirtualDuty (): ?string;

    public function setVirtualDuty (?string $virtualDuty): DigitalDocumentInstanceInterface;

    public function getVirtualDutyAmount (): ?float;

    public function setVirtualDutyAmount (?float $virtualDutyAmount): DigitalDocumentInstanceInterface;

    public function getDocumentType (): ?DocumentType;

    public function setDocumentType ($documentType): DigitalDocumentInstanceInterface;

    public function getDocumentNumber (): ?string;

    public function setDocumentNumber (?string $documentNumber): DigitalDocumentInstanceInterface;

    public function getAmount (): ?float;

    public function setAmount (?float $amount): DigitalDocumentInstanceInterface;

    public function getAmountTax (): ?float;

    public function setAmountTax (?float $amountTax): DigitalDocumentInstanceInterface;

    public function getDocumentTotal (): ?float;

    public function setDocumentTotal (?float $documentTotal): DigitalDocumentInstanceInterface;

    public function hasDeduction (): bool;

    public function addFund (FundInterface $fund): DigitalDocumentInstanceInterface;

    /**
     * @return Fund[]
     */
    public function getFunds (): array;

    public function hasFunds (): bool;

    public function addDiscount (DiscountInterface $discount): DigitalDocumentInstanceInterface;

    /**
     * @return DiscountInterface[]
     */
    public function getDiscounts (): array;

    public function hasDiscounts (): bool;

    public function hasPurchaseOrders (): bool;

    public function getPurchaseOrders (): array;

    public function addPurchaseOrder (RelatedDocumentInterface $document): DigitalDocumentInstanceInterface;


    public function getConventions (): array;

    public function addConvention (RelatedDocumentInterface $document): DigitalDocumentInstanceInterface;


    public function hasConventions (): bool;

    public function getReceipts (): array;

    public function addReceipt (RelatedDocumentInterface $document): DigitalDocumentInstanceInterface;

    public function hasReceipts (): bool;

    public function getRelatedInvoices (): array;

    public function addRelatedInvoice (RelatedDocumentInterface $document): DigitalDocumentInstanceInterface;

    public function hasRelatedInvoices (): bool;

    public function getContracts (): array;

    public function addContract (RelatedDocumentInterface $document): DigitalDocumentInstanceInterface;

    public function hasContracts (): bool;

    public function addSal (int $sal): DigitalDocumentInstanceInterface;

    public function getSals (): array;

    public function hasSals(): bool;

}