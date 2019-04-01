<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentMethod;
use FatturaElettronicaPhp\FatturaElettronica\PaymentInfo;

interface PaymentDetailsInterface
{
    /**
     * @return string
     */
    public function getPayee (): ?string;

    /**
     * @param string $payee
     * @return PaymentInfo
     */
    public function setPayee (?string $payee);

    /**
     * @return string
     */
    public function getMethod (): ?PaymentMethod;

    /**
     * @param string $method
     * @return PaymentInfo
     */
    public function setMethod ($method);

    /**
     * @return DateTime
     */
    public function getDueDateFrom (): ?DateTime;

    /**
     * @param DateTime $dueDateFrom
     * @return PaymentInfo
     */
    public function setDueDateFrom ($dueDateFrom, $format = null);

    /**
     * @return int
     */
    public function getDueDays (): ?int;

    /**
     * @param int $dueDays
     * @return PaymentInfo
     */
    public function setDueDays (?int $dueDays);

    /**
     * @return DateTime
     */
    public function getDueDate (): ?DateTime;

    /**
     * @param DateTime $dueDate
     * @return PaymentInfo
     */
    public function setDueDate ($dueDate, $format = null);

    /**
     * @return float
     */
    public function getAmount (): ?float;

    /**
     * @param float $amount
     * @return PaymentInfo
     */
    public function setAmount (?float $amount);

    /**
     * @return string
     */
    public function getPostalOfficeCode (): ?string;

    /**
     * @param string $postalOfficeCode
     * @return PaymentInfo
     */
    public function setPostalOfficeCode (?string $postalOfficeCode);

    /**
     * @return string
     */
    public function getPayerSurname (): ?string;

    /**
     * @param string $payerSurname
     * @return PaymentInfo
     */
    public function setPayerSurname (?string $payerSurname);

    /**
     * @return string
     */
    public function getPayerName (): ?string;

    /**
     * @param string $payerName
     * @return PaymentInfo
     */
    public function setPayerName (?string $payerName);

    /**
     * @return string
     */
    public function getPayerFiscalCode (): ?string;

    /**
     * @param string $payerFiscalCode
     * @return PaymentInfo
     */
    public function setPayerFiscalCode (?string $payerFiscalCode);

    /**
     * @return string
     */
    public function getPayerTitle (): ?string;

    /**
     * @param string $payerTitle
     * @return PaymentInfo
     */
    public function setPayerTitle (?string $payerTitle);

    /**
     * @return string
     */
    public function getBankName (): ?string;

    /**
     * @param string $bankName
     * @return PaymentInfo
     */
    public function setBankName (?string $bankName);

    /**
     * @return string
     */
    public function getIban (): ?string;

    /**
     * @param string $iban
     * @return PaymentInfo
     */
    public function setIban (?string $iban);

    /**
     * @return string
     */
    public function getAbi (): ?string;

    /**
     * @param string $abi
     * @return PaymentInfo
     */
    public function setAbi (?string $abi);

    /**
     * @return string
     */
    public function getCab (): ?string;

    /**
     * @param string $cab
     * @return PaymentInfo
     */
    public function setCab (?string $cab);

    /**
     * @return string
     */
    public function getBic (): ?string;

    /**
     * @param string $bic
     * @return PaymentInfo
     */
    public function setBic (?string $bic);

    /**
     * @return float
     */
    public function getEarlyPaymentDiscount (): ?float;

    /**
     * @param float $earlyPaymentDiscount
     * @return PaymentInfo
     */
    public function setEarlyPaymentDiscount (?float $earlyPaymentDiscount);

    /**
     * @return DateTime
     */
    public function getEarlyPaymentDateLimit (): ?DateTime;

    /**
     * @param DateTime $earlyPaymentDateLimit
     * @return PaymentInfo
     */
    public function setEarlyPaymentDateLimit ($earlyPaymentDateLimit, $format = null);

    /**
     * @return float
     */
    public function getLatePaymentFee (): ?float;

    /**
     * @param float $latePaymentFee
     * @return PaymentInfo
     */
    public function setLatePaymentFee (?float $latePaymentFee);

    /**
     * @return DateTime
     */
    public function getLatePaymentDateLimit (): ?DateTime;

    /**
     * @param DateTime $latePaymentDateLimit
     * @return PaymentInfo
     */
    public function setLatePaymentDateLimit ($latePaymentDateLimit, $format = null);

    /**
     * @return string
     */
    public function getPaymentCode (): ?string;

    /**
     * @param string $paymentCode
     * @return PaymentInfo
     */
    public function setPaymentCode (?string $paymentCode);
}