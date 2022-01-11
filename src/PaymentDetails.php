<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentDetailsInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentMethod;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class PaymentDetails implements ArrayableInterface, PaymentDetailsInterface
{
    use Arrayable;

    /** @var string */
    protected $payee;
    /** @var PaymentMethod */
    protected $method;
    /** @var DateTime */
    protected $dueDateFrom;
    /** @var int */
    protected $dueDays;
    /** @var DateTime */
    protected $dueDate;
    /** @var float */
    protected $amount;
    /** @var string */
    protected $postalOfficeCode;
    /** @var string */
    protected $payerSurname;
    /** @var string */
    protected $payerName;
    /** @var string */
    protected $payerFiscalCode;
    /** @var string */
    protected $payerTitle;
    /** @var string */
    protected $bankName;
    /** @var string */
    protected $iban;
    /** @var string */
    protected $abi;
    /** @var string */
    protected $cab;
    /** @var string */
    protected $bic;
    /** @var float */
    protected $earlyPaymentDiscount;
    /** @var DateTime */
    protected $earlyPaymentDateLimit;
    /** @var float */
    protected $latePaymentFee;
    /** @var DateTime */
    protected $latePaymentDateLimit;
    /** @var string */
    protected $paymentCode;

    /**
     * @return string
     */
    public function getPayee(): ?string
    {
        return $this->payee;
    }

    /**
     * @param string $payee
     * @return PaymentInfo
     */
    public function setPayee(?string $payee): self
    {
        $this->payee = $payee;

        return $this;
    }

    /**
     * @return PaymentMethod
     */
    public function getMethod(): ?PaymentMethod
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return PaymentInfo
     */
    public function setMethod($method): self
    {
        if ($method === null) {
            return $this;
        }

        if (! $method instanceof PaymentMethod) {
            $method = new PaymentMethod($method);
        }

        $this->method = $method;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDueDateFrom(): ?DateTime
    {
        return $this->dueDateFrom;
    }

    /**
     * @param DateTime $dueDateFrom
     * @return PaymentInfo
     */
    public function setDueDateFrom($dueDateFrom, $format = null): self
    {
        if ($dueDateFrom === null) {
            return $this;
        }

        if ($format !== null) {
            $this->dueDateFrom = DateTime::createFromFormat($format, $dueDateFrom);

            return $this;
        }

        if ($dueDateFrom instanceof DateTime) {
            $this->dueDateFrom = $dueDateFrom;

            return $this;
        }

        $this->dueDateFrom = new DateTime($dueDateFrom);

        return $this;
    }

    /**
     * @return int
     */
    public function getDueDays(): ?int
    {
        return $this->dueDays;
    }

    /**
     * @param int $dueDays
     * @return PaymentInfo
     */
    public function setDueDays(?int $dueDays): self
    {
        $this->dueDays = $dueDays;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDueDate(): ?DateTime
    {
        return $this->dueDate;
    }

    /**
     * @param DateTime $dueDate
     * @return PaymentInfo
     */
    public function setDueDate($dueDate, $format = null): self
    {
        if ($dueDate === null) {
            return $this;
        }

        if ($format !== null) {
            $this->dueDate = DateTime::createFromFormat($format, $dueDate);

            return $this;
        }

        if ($dueDate instanceof DateTime) {
            $this->dueDate = $dueDate;

            return $this;
        }

        $this->dueDate = new DateTime($dueDate);

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return PaymentInfo
     */
    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostalOfficeCode(): ?string
    {
        return $this->postalOfficeCode;
    }

    /**
     * @param string $postalOfficeCode
     * @return PaymentInfo
     */
    public function setPostalOfficeCode(?string $postalOfficeCode): self
    {
        $this->postalOfficeCode = $postalOfficeCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerSurname(): ?string
    {
        return $this->payerSurname;
    }

    /**
     * @param string $payerSurname
     * @return PaymentInfo
     */
    public function setPayerSurname(?string $payerSurname): self
    {
        $this->payerSurname = $payerSurname;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerName(): ?string
    {
        return $this->payerName;
    }

    /**
     * @param string $payerName
     * @return PaymentInfo
     */
    public function setPayerName(?string $payerName): self
    {
        $this->payerName = $payerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerFiscalCode(): ?string
    {
        return $this->payerFiscalCode;
    }

    /**
     * @param string $payerFiscalCode
     * @return PaymentInfo
     */
    public function setPayerFiscalCode(?string $payerFiscalCode): self
    {
        $this->payerFiscalCode = $payerFiscalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerTitle(): ?string
    {
        return $this->payerTitle;
    }

    /**
     * @param string $payerTitle
     * @return PaymentInfo
     */
    public function setPayerTitle(?string $payerTitle): self
    {
        $this->payerTitle = $payerTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     * @return PaymentInfo
     */
    public function setBankName(?string $bankName): self
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * @return string
     */
    public function getIban(): ?string
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     * @return PaymentInfo
     */
    public function setIban(?string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * @return string
     */
    public function getAbi(): ?string
    {
        return $this->abi;
    }

    /**
     * @param string $abi
     * @return PaymentInfo
     */
    public function setAbi(?string $abi): self
    {
        $this->abi = $abi;

        return $this;
    }

    /**
     * @return string
     */
    public function getCab(): ?string
    {
        return $this->cab;
    }

    /**
     * @param string $cab
     * @return PaymentInfo
     */
    public function setCab(?string $cab): self
    {
        $this->cab = $cab;

        return $this;
    }

    /**
     * @return string
     */
    public function getBic(): ?string
    {
        return $this->bic;
    }

    /**
     * @param string $bic
     * @return PaymentInfo
     */
    public function setBic(?string $bic): self
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * @return float
     */
    public function getEarlyPaymentDiscount(): ?float
    {
        return $this->earlyPaymentDiscount;
    }

    /**
     * @param float $earlyPaymentDiscount
     * @return PaymentInfo
     */
    public function setEarlyPaymentDiscount(?float $earlyPaymentDiscount): self
    {
        $this->earlyPaymentDiscount = $earlyPaymentDiscount;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEarlyPaymentDateLimit(): ?DateTime
    {
        return $this->earlyPaymentDateLimit;
    }

    /**
     * @param DateTime $earlyPaymentDateLimit
     * @return PaymentInfo
     */
    public function setEarlyPaymentDateLimit($earlyPaymentDateLimit, $format = null): self
    {
        if ($earlyPaymentDateLimit === null) {
            return $this;
        }

        if ($format !== null) {
            $this->earlyPaymentDateLimit = DateTime::createFromFormat($format, $earlyPaymentDateLimit);

            return $this;
        }

        if ($earlyPaymentDateLimit instanceof DateTime) {
            $this->earlyPaymentDateLimit = $earlyPaymentDateLimit;

            return $this;
        }

        $this->earlyPaymentDateLimit = new DateTime($earlyPaymentDateLimit);

        return $this;
    }

    /**
     * @return float
     */
    public function getLatePaymentFee(): ?float
    {
        return $this->latePaymentFee;
    }

    /**
     * @param float $latePaymentFee
     * @return PaymentInfo
     */
    public function setLatePaymentFee(?float $latePaymentFee): self
    {
        $this->latePaymentFee = $latePaymentFee;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLatePaymentDateLimit(): ?DateTime
    {
        return $this->latePaymentDateLimit;
    }

    /**
     * @param DateTime $latePaymentDateLimit
     * @return PaymentInfo
     */
    public function setLatePaymentDateLimit($latePaymentDateLimit, $format = null): self
    {
        if ($latePaymentDateLimit === null) {
            return $this;
        }

        if ($format !== null) {
            $this->latePaymentDateLimit = DateTime::createFromFormat($format, $latePaymentDateLimit);

            return $this;
        }

        if ($latePaymentDateLimit instanceof DateTime) {
            $this->latePaymentDateLimit = $latePaymentDateLimit;

            return $this;
        }

        $this->latePaymentDateLimit = new DateTime($latePaymentDateLimit);

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentCode(): ?string
    {
        return $this->paymentCode;
    }

    /**
     * @param string $paymentCode
     * @return PaymentInfo
     */
    public function setPaymentCode(?string $paymentCode): self
    {
        $this->paymentCode = $paymentCode;

        return $this;
    }
}
