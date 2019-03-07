<?php

namespace Weble\FatturaElettronica\Contracts;

use DateTime;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use Weble\FatturaElettronica\Enums\DocumentType;

interface DigitalDocumentInstanceInterface
{
    public function getDocumentDate (): DateTime;

    public function setDocumentDate ($documentDate, $format = null): DigitalDocumentInstanceInterface;

    public function getDocumentType (): DocumentType;

    public function setDocumentType ($documentType): DigitalDocumentInstanceInterface;

    public function getDocumentNumber (): string;

    public function setDocumentNumber (string $documentNumber): DigitalDocumentInstanceInterface;

    public function getAmount (): float;

    public function setAmount (float $amount): DigitalDocumentInstanceInterface;

    public function getAmountTax (): float;

    public function setAmountTax (float $amountTax): DigitalDocumentInstanceInterface;

    public function getDocumentTotal (): float;

    public function setDocumentTotal (float $documentTotal): DigitalDocumentInstanceInterface;
}