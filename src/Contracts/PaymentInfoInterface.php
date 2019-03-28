<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentTerm;
use FatturaElettronicaPhp\FatturaElettronica\PaymentInfo;

interface PaymentInfoInterface
{
    /**
     * @return PaymentTerm
     */
    public function getTerms (): ?PaymentTerm;

    /**
     * @param PaymentTerm $terms
     * @return PaymentInfo
     */
    public function setTerms ($terms);

    public function addDetails (PaymentDetailsInterface $details);

    public function getDetails (): array;

    public function hasDetails (): bool;
}