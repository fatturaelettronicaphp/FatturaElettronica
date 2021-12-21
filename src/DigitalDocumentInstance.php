<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AttachmentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DeductionInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\FundInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\LineInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentInfoInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\RelatedDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\SimplifiedLineInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\TotalInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DeductionType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentType;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class DigitalDocumentInstance implements ArrayableInterface, DigitalDocumentInstanceInterface
{
    use Arrayable;

    /** @var DocumentType */
    protected $documentType;

    /** @var string */
    protected $documentNumber;

    /** @var float */
    protected $rounding;

    /** @var string[] */
    protected $descriptions = [];

    /** @var DateTime */
    protected $documentDate;

    /** @var bool */
    protected $art73 = false;

    /** @var string */
    protected $currency;

    /** @var DeductionType */
    protected $deductionType;

    /** @var float */
    protected $deductionAmount;

    /** @var float */
    protected $deductionPercentage;

    /** @var string */
    protected $deductionDescription;

    /** @var string */
    protected $virtualDuty;

    /** @var float */
    protected $virtualDutyAmount;

    /** @var DeductionInterface[] */
    protected $deductions = [];

    /** @var FundInterface[] */
    protected $funds = [];

    /** @var DiscountInterface[] */
    protected $discounts = [];

    /** @var RelatedDocumentInterface[] */
    protected $purchaseOrders = [];

    /** @var RelatedDocumentInterface[] */
    protected $conventions = [];

    /** @var RelatedDocumentInterface[] */
    protected $receipts = [];

    /** @var RelatedDocumentInterface[] */
    protected $relatedInvoices = [];

    /** @var RelatedDocumentInterface[] */
    protected $contracts = [];

    /** @var int[] */
    protected $sals = [];

    /** @var LineInterface[] */
    protected $lines = [];

    /** @var SimplifiedLineInterface[] */
    protected $simplifiedLines = [];

    /** @var ShippingLabel[] */
    protected $shippingLabels = [];

    /** @var Shipment */
    protected $shipment;

    /** @var string */
    protected $mainInvoiceNumber;

    /** @var DateTime */
    protected $mainInvoiceDate;

    /** @var Total[] */
    protected $totals = [];

    /** @var float */
    protected $documentTotal;

    /** @var DateTime */
    protected $vehicleRegistrationDate;

    /** @var string */
    protected $vehicleTotalKm;

    /** @var PaymentInfoInterface[] */
    protected $paymentInformations = [];

    /** @var AttachmentInterface[] */
    protected $attachments = [];

    public function getRounding(): ?float
    {
        return $this->rounding;
    }

    public function setRounding(?float $rounding): DigitalDocumentInstanceInterface
    {
        $this->rounding = $rounding;

        return $this;
    }

    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function addDescription(string $description): DigitalDocumentInstanceInterface
    {
        $this->descriptions[] = $description;

        return $this;
    }

    public function getDocumentDate(): ?DateTime
    {
        return $this->documentDate;
    }

    public function setDocumentDate($documentDate, $format = null): DigitalDocumentInstanceInterface
    {
        if ($documentDate === null) {
            return $this;
        }

        if ($format !== null) {
            $this->documentDate = DateTime::createFromFormat($format, $documentDate);

            return $this;
        }

        if ($documentDate instanceof DateTime) {
            $this->documentDate = $documentDate;

            return $this;
        }

        $this->documentDate = new DateTime($documentDate);

        return $this;
    }

    public function isArt73(): bool
    {
        return $this->art73;
    }

    public function setArt73(?bool $art73): DigitalDocumentInstanceInterface
    {
        $art73       = (bool)$art73;
        $this->art73 = $art73;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): DigitalDocumentInstanceInterface
    {
        $this->currency = $currency;

        return $this;
    }

    public function getDeductionType(): ?DeductionType
    {
    	if( $this->hasMultipleDeductions() )
    		return $this->getDeductions()[0]->getType();

        return $this->deductionType;
    }

    public function setDeductionType($deductionType): DigitalDocumentInstanceInterface
    {
        if ($deductionType === null) {
            return $this;
        }

        if (! $deductionType instanceof DeductionType) {
            $deductionType = new DeductionType($deductionType);
        }

        $this->deductionType = $deductionType;

        return $this;
    }

    public function getDeductionAmount(): ?float
    {
		if( $this->hasMultipleDeductions() )
			return $this->getDeductions()[0]->getAmount();

		return $this->deductionAmount;
    }

    public function setDeductionAmount(?float $deductionAmount): DigitalDocumentInstanceInterface
    {
        $this->deductionAmount = $deductionAmount;

        return $this;
    }

    public function getDeductionPercentage(): ?float
    {
		if( $this->hasMultipleDeductions() )
			return $this->getDeductions()[0]->getPercentage();

        return $this->deductionPercentage;
    }

    public function setDeductionPercentage(?float $deductionPercentage): DigitalDocumentInstanceInterface
    {
		$this->deductionPercentage = $deductionPercentage;

        return $this;
    }

    public function getDeductionDescription(): ?string
    {
		if( $this->hasMultipleDeductions() )
			return $this->getDeductions()[0]->getDescription();

		return $this->deductionDescription;
    }

    public function setDeductionDescription(?string $deductionDescription): DigitalDocumentInstanceInterface
    {
        $this->deductionDescription = $deductionDescription;

        return $this;
    }

    public function getVirtualDuty(): ?string
    {
        return $this->virtualDuty;
    }

    public function setVirtualDuty(?string $virtualDuty): DigitalDocumentInstanceInterface
    {
        $this->virtualDuty = $virtualDuty;

        return $this;
    }

    public function getVirtualDutyAmount(): ?float
    {
        return $this->virtualDutyAmount;
    }

    public function setVirtualDutyAmount(?float $virtualDutyAmount): DigitalDocumentInstanceInterface
    {
        $this->virtualDutyAmount = $virtualDutyAmount;

        return $this;
    }

    public function getDocumentType(): ?DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType($documentType): DigitalDocumentInstanceInterface
    {
        if ($documentType === null) {
            return $this;
        }

        if (! $documentType instanceof DocumentType) {
            $documentType = new DocumentType($documentType);
        }

        $this->documentType = $documentType;

        return $this;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(?string $documentNumber): DigitalDocumentInstanceInterface
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    public function hasDeduction(): bool
    {
        if (
            $this->deductionAmount !== null ||
            $this->deductionDescription !== null ||
            $this->deductionPercentage !== null ||
            $this->deductionType !== null
        ) {
            return true;
        }

        return false;
    }

	public function hasMultipleDeductions(): bool
	{
		return count($this->deductions) > 0;
	}

    public function addDeduction(DeductionInterface $deduction): DigitalDocumentInstanceInterface
	{
		$this->deductions[] = $deduction;

		return $this;
	}

	/**
	 * @return DeductionInterface[]
	 */
	public function getDeductions(): array
	{
		return $this->deductions;
	}

    public function addFund(FundInterface $fund): DigitalDocumentInstanceInterface
    {
        $this->funds[] = $fund;

        return $this;
    }

    /**
     * @return FundInterface[]
     */
    public function getFunds(): array
    {
        return $this->funds;
    }

    public function hasFunds(): bool
    {
        if (count($this->funds) > 0) {
            return true;
        }

        return false;
    }

    public function addDiscount(DiscountInterface $discount): DigitalDocumentInstanceInterface
    {
        $this->discounts[] = $discount;

        return $this;
    }

    /**
     * @return DiscountInterface[]
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    public function hasDiscounts(): bool
    {
        if (count($this->discounts) > 0) {
            return true;
        }

        return false;
    }

    public function hasPurchaseOrders(): bool
    {
        return count($this->purchaseOrders) > 0;
    }

    public function getPurchaseOrders(): array
    {
        return $this->purchaseOrders;
    }

    public function addPurchaseOrder(RelatedDocumentInterface $document): DigitalDocumentInstanceInterface
    {
        $this->purchaseOrders[] = $document;

        return $this;
    }

    public function getConventions(): array
    {
        return $this->conventions;
    }

    public function addConvention(RelatedDocumentInterface $document): DigitalDocumentInstanceInterface
    {
        $this->conventions[] = $document;

        return $this;
    }

    public function hasConventions(): bool
    {
        return count($this->conventions) > 0;
    }

    public function getContracts(): array
    {
        return $this->contracts;
    }

    public function addContract(RelatedDocumentInterface $document): DigitalDocumentInstanceInterface
    {
        $this->contracts[] = $document;

        return $this;
    }

    public function hasContracts(): bool
    {
        return count($this->contracts) > 0;
    }

    public function getReceipts(): array
    {
        return $this->receipts;
    }

    public function addReceipt(RelatedDocumentInterface $document): DigitalDocumentInstanceInterface
    {
        $this->receipts[] = $document;

        return $this;
    }

    public function hasReceipts(): bool
    {
        return count($this->receipts) > 0;
    }

    public function getRelatedInvoices(): array
    {
        return $this->relatedInvoices;
    }

    public function addRelatedInvoice(RelatedDocumentInterface $document): DigitalDocumentInstanceInterface
    {
        $this->relatedInvoices[] = $document;

        return $this;
    }

    public function hasRelatedInvoices(): bool
    {
        return count($this->relatedInvoices) > 0;
    }

    public function addSal(int $sal): DigitalDocumentInstanceInterface
    {
        $this->sals[] = $sal;

        return $this;
    }

    public function getSals(): array
    {
        return $this->sals;
    }

    public function addLine(LineInterface $line): DigitalDocumentInstanceInterface
    {
        $this->lines[] = $line;

        return $this;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function setSimplifiedLine(SimplifiedLineInterface $line): DigitalDocumentInstanceInterface
    {
        $this->simplifiedLines = [$line];

        return $this;
    }

    public function addSimplifiedLine(SimplifiedLineInterface $line): DigitalDocumentInstanceInterface
    {
        $this->simplifiedLines[] = $line;

        return $this;
    }

    public function getSimplifiedLine(): ?SimplifiedLineInterface
    {
        return $this->simplifiedLines[0] ?? null;
    }

    public function getSimplifiedLines(): array
    {
        return $this->simplifiedLines ?? [];
    }

    public function hasSals(): bool
    {
        return count($this->sals) > 0;
    }

    public function getShippingLabels(): array
    {
        return $this->shippingLabels;
    }

    public function addShippingLabel(ShippingLabel $document): DigitalDocumentInstanceInterface
    {
        $this->shippingLabels[] = $document;

        return $this;
    }

    public function getPaymentInformations(): array
    {
        return $this->paymentInformations;
    }

    public function addPaymentInformations(PaymentInfoInterface $paymentInfo): DigitalDocumentInstanceInterface
    {
        $this->paymentInformations[] = $paymentInfo;

        return $this;
    }

    public function hasPaymentInformations(): bool
    {
        return (count($this->paymentInformations) > 0);
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function addAttachment(AttachmentInterface $attachment): DigitalDocumentInstanceInterface
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    public function hasAttachments(): bool
    {
        return (count($this->attachments) > 0);
    }

    public function getTotals(): array
    {
        return $this->totals;
    }

    public function setDocumentTotal(?float $documentTotal): self
    {
        $this->documentTotal = $documentTotal;

        return $this;
    }

    public function getDocumentTotal(): ?float
    {
        return $this->documentTotal;
    }

    public function calculateDocumentTotalTaxAmount(): float
    {
        $total = 0;
        /** @var TotalInterface $total */
        foreach ($this->getTotals() as $t) {
            $total += $t->getTaxAmount();
        }

        return $total;
    }

    public function addTotal(TotalInterface $total): DigitalDocumentInstanceInterface
    {
        $this->totals[] = $total;

        return $this;
    }

    public function hasShippingLabels(): bool
    {
        return count($this->shippingLabels) > 0;
    }

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function setShipment(?Shipment $shipment): DigitalDocumentInstanceInterface
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function getMainInvoiceNumber(): ?string
    {
        return $this->mainInvoiceNumber;
    }

    public function setMainInvoiceNumber(?string $mainInvoiceNumber): DigitalDocumentInstanceInterface
    {
        $this->mainInvoiceNumber = $mainInvoiceNumber;

        return $this;
    }

    public function getMainInvoiceDate(): ?DateTime
    {
        return $this->mainInvoiceDate;
    }

    public function setMainInvoiceDate($mainInvoiceDate, $format = null): DigitalDocumentInstanceInterface
    {
        if ($mainInvoiceDate === null) {
            return $this;
        }

        if ($format !== null) {
            $this->mainInvoiceDate = DateTime::createFromFormat($format, $mainInvoiceDate);

            return $this;
        }

        if ($mainInvoiceDate instanceof DateTime) {
            $this->mainInvoiceDate = $mainInvoiceDate;

            return $this;
        }

        $this->mainInvoiceDate = new DateTime($mainInvoiceDate);

        return $this;
    }

    public function getVehicleRegistrationDate(): ?DateTime
    {
        return $this->vehicleRegistrationDate;
    }

    public function setVehicleRegistrationDate($vehicleRegistrationDate, $format = null): self
    {
        if ($vehicleRegistrationDate === null) {
            return $this;
        }

        if ($format !== null) {
            $this->vehicleRegistrationDate = DateTime::createFromFormat($format, $vehicleRegistrationDate);

            return $this;
        }

        if ($vehicleRegistrationDate instanceof DateTime) {
            $this->vehicleRegistrationDate = $vehicleRegistrationDate;

            return $this;
        }

        $this->vehicleRegistrationDate = new DateTime($vehicleRegistrationDate);

        return $this;
    }

    public function getVehicleTotalKm(): ?string
    {
        return $this->vehicleTotalKm;
    }

    public function setVehicleTotalKm(?string $vehicleTotalKm): self
    {
        $this->vehicleTotalKm = $vehicleTotalKm;

        return $this;
    }

    /**
     * @return float|int|null
     */
    public function calculateDocumentTotal(): float
    {
        $total = 0;
        /** @var TotalInterface $t */
        foreach ($this->getTotals() as $t) {
            $total += $t->getTotal();
        }

        return $total;
    }
}
