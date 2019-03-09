<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\PaymentDetailsInterface;
use Weble\FatturaElettronica\Contracts\PaymentInfoInterface;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;
use Weble\FatturaElettronica\Enums\PaymentTerm;
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
