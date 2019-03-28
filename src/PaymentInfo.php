<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentDetailsInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentInfoInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentTerm;
use DateTime;

class PaymentInfo implements ArrayableInterface, PaymentInfoInterface
{
    use Arrayable;

    /** @var PaymentTerm */
    protected $terms;

    /** @var PaymentDetailsInterface[] */
    protected $details = [];

    /**
     * @return PaymentTerm
     */
    public function getTerms (): ?PaymentTerm
    {
        return $this->terms;
    }

    /**
     * @param PaymentTerm $terms
     * @return PaymentInfo
     */
    public function setTerms ($terms): self
    {
        if ($terms === null) {
            return $this;
        }

        if (!$terms instanceof PaymentTerm) {
            $terms = PaymentTerm::from($terms);
        }

        $this->terms = $terms;
        return $this;
    }

    public function addDetails (PaymentDetailsInterface $details): self
    {
        $this->details[] = $details;
        return $this;
    }

    public function getDetails (): array
    {
        return $this->details;
    }

    public function hasDetails (): bool
    {
        return count($this->details) > 0;
    }
}
