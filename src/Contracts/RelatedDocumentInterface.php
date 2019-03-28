<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\RelatedDocument;

interface RelatedDocumentInterface
{
    public function getLineNumberReference (): ?string;

    public function setLineNumberReference (?string $lineNumberReference);

    public function getDocumentNumber (): ?string;

    public function setDocumentNumber (?string $documentNumber);

    public function getDocumentDate (): ?DateTime;

    public function setDocumentDate ($documentDate, $format = null);

    public function getLineNumber (): ?string;

    public function setLineNumber (?string $lineNumber): RelatedDocument;

    public function getOrderCode (): ?string;

    public function setOrderCode (?string $orderCode): RelatedDocument;

    public function getCupCode (): ?string;

    public function setCupCode (?string $cupCode): RelatedDocument;

    public function getCigCode (): ?string;

    public function setCigCode (?string $cigCode): RelatedDocument;
}