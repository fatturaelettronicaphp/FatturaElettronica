<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillablePersonInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\CustomerInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentValidatorInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\IntermediaryInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\SupplierInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\EmittingSubject;
use FatturaElettronicaPhp\FatturaElettronica\Enums\RecipientCode;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat;
use FatturaElettronicaPhp\FatturaElettronica\Parser\DigitalDocumentParser;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Validator\DigitalDocumentValidator;
use FatturaElettronicaPhp\FatturaElettronica\Validator\MultipleDigitalDocumentValidator;
use FatturaElettronicaPhp\FatturaElettronica\Validator\SdiValidator;
use FatturaElettronicaPhp\FatturaElettronica\Writer\DigitalDocumentWriter;
use FatturaElettronicaPhp\FatturaElettronica\Writer\SimplifiedDigitalDocumentWriter;
use SimpleXMLElement;

class DigitalDocument implements ArrayableInterface, DigitalDocumentInterface
{
    use Arrayable;

    /** @var CustomerInterface */
    protected $customer;

    /** @var SupplierInterface */
    protected $supplier;

    /** @var string */
    protected $emittingSubject;

    /** @var BillablePersonInterface */
    protected $representative;

    /** @var IntermediaryInterface */
    protected $intermediary;

    /** @var TransmissionFormat */
    protected $transmissionFormat;

    /** @var string */
    protected $countryCode;

    /** @var string */
    protected $senderVatId;

    /** @var string */
    protected $sendingId;

    /** @var string */
    protected $customerSdiCode;

    /** @var string */
    protected $senderPhone;

    /** @var string */
    protected $senderEmail;

    /** @var string */
    protected $customerPec;

    /** @var DigitalDocumentInstance[] */
    protected $documentInstances = [];

    /** @var string|null */
    protected $version;

    /** @var string|null */
    protected $emittingSystem;

    /** @var class-string<DigitalDocumentValidatorInterface>[] */
    protected array $validators = [
        DigitalDocumentValidator::class,
        SdiValidator::class,
    ];

    public function __construct()
    {
        $this->customerSdiCode = RecipientCode::EMPTY;
    }

    /**
     * @param class-string<DigitalDocumentValidatorInterface> $validator
     * @return self
     */
    public function addValidator(string $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    public function withoutValidators(): self
    {
        $this->validators = [];

        return $this;
    }

    /**
     * @param string|SimpleXMLElement $xml
     * @return DigitalDocumentInterface
     */
    public static function parseFrom($xml): DigitalDocumentInterface
    {
        return (new DigitalDocumentParser($xml))->parse();
    }

    public function serialize(): SimpleXMLElement
    {
        if ($this->isSimplified()) {
            return (new SimplifiedDigitalDocumentWriter($this))->generate()->xml();
        }

        return (new DigitalDocumentWriter($this))->generate()->xml();
    }

    public function write(string $filePath, bool $format = false): bool
    {
        if ($this->isSimplified()) {
            return (new SimplifiedDigitalDocumentWriter($this))->write($filePath, $format);
        }

        return (new DigitalDocumentWriter($this))->write($filePath, $format);
    }

    public function generatedFilename(): string
    {
        return $this->getCountryCode() . $this->getSenderVatId() . '_' . $this->getSendingId() . '.xml';
    }

    public function validate(): DigitalDocumentValidatorInterface
    {
        return (new MultipleDigitalDocumentValidator($this))
            ->withValidators($this->validators)
            ->validate();
    }

    public function isValid(): bool
    {
        return $this->validate()->isValid();
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getEmittingSystem(): ?string
    {
        return $this->emittingSystem;
    }

    public function setEmittingSystem(?string $system): self
    {
        $this->emittingSystem = $system;

        return $this;
    }

    public function getEmittingSubject()
    {
        return $this->emittingSubject;
    }

    public function setEmittingSubject($emittingSubject): self
    {
        if ($emittingSubject === null) {
            return $this;
        }

        if (! $emittingSubject instanceof EmittingSubject) {
            $emittingSubject = new EmittingSubject($emittingSubject);
        }

        $this->emittingSubject = $emittingSubject;

        return $this;
    }

    public function getRepresentative(): ?BillablePersonInterface
    {
        return $this->representative;
    }

    public function setRepresentative(?BillablePersonInterface $representative): self
    {
        $this->representative = $representative;

        return $this;
    }

    public function getIntermediary(): ?IntermediaryInterface
    {
        return $this->intermediary;
    }

    public function setIntermediary(?IntermediaryInterface $intermediary): self
    {
        $this->intermediary = $intermediary;

        return $this;
    }

    public function addDigitalDocumentInstance(DigitalDocumentInstanceInterface $instance): self
    {
        $this->documentInstances[] = $instance;

        return $this;
    }

    public function getDocumentInstances(): array
    {
        return $this->documentInstances;
    }

    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function setCountryCode($countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCustomerSdiCode()
    {
        return $this->customerSdiCode;
    }

    public function setCustomerSdiCode($customerSdiCode): self
    {
        $this->customerSdiCode = $customerSdiCode;

        return $this;
    }

    public function getSenderVatId()
    {
        return $this->senderVatId;
    }

    public function setSenderVatId($senderVatId): self
    {
        $this->senderVatId = $senderVatId;

        return $this;
    }

    public function getSendingId()
    {
        return $this->sendingId;
    }

    public function setSendingId($sendingId): self
    {
        $this->sendingId = $sendingId;

        return $this;
    }

    public function getSenderPhone()
    {
        return $this->senderPhone;
    }

    public function setSenderPhone($senderPhone): self
    {
        $this->senderPhone = $senderPhone;

        return $this;
    }

    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    public function setSenderEmail($senderEmail): self
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    public function getCustomerPec()
    {
        return $this->customerPec;
    }

    public function setCustomerPec($customerPec): self
    {
        $this->customerPec = $customerPec;

        return $this;
    }

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getSupplier(): ?SupplierInterface
    {
        return $this->supplier;
    }

    public function setSupplier(SupplierInterface $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getTransmissionFormat(): ?TransmissionFormat
    {
        return $this->transmissionFormat;
    }

    public function setTransmissionFormat($transmissionFormat): self
    {
        if ($transmissionFormat === null) {
            return $this;
        }

        if (! $transmissionFormat instanceof TransmissionFormat) {
            $transmissionFormat = new TransmissionFormat($transmissionFormat);
        }

        $this->transmissionFormat = $transmissionFormat;

        return $this;
    }

    public function isSimplified(): bool
    {
        return $this->transmissionFormat->equals(TransmissionFormat::FSM10());
    }
}
