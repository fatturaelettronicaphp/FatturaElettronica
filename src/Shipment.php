<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AddressInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Shipment implements ArrayableInterface
{
    use Arrayable;

    /** @var BillableInterface */
    protected $shipper;
    /** @var string */
    protected $method;
    /** @var string */
    protected $description;
    /** @var string */
    protected $shipmentDescription;
    /** @var int */
    protected $numberOfPackages;
    /** @var string */
    protected $weightUnit;
    /** @var float */
    protected $weight;
    /** @var float */
    protected $netWeight;
    /** @var DateTime */
    protected $pickupDate;
    /** @var DateTime */
    protected $shipmentDate;
    /** @var DateTime */
    protected $deliveryDate;
    /** @var string */
    protected $returnType;
    /** @var AddressInterface */
    protected $returnAddress;

    /**
     * @return BillableInterface
     */
    public function getShipper (): ?BillableInterface
    {
        return $this->shipper;
    }

    /**
     * @param BillableInterface $shipper
     *
     * @return Shipment
     */
    public function setShipper (?BillableInterface $shipper): Shipment
    {
        $this->shipper = $shipper;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod (): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return Shipment
     */
    public function setMethod (?string $method): Shipment
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription (): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Shipment
     */
    public function setDescription (?string $description): Shipment
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getShipmentDescription (): ?string
    {
        return $this->shipmentDescription;
    }

    /**
     * @param string $shipmentDescription
     *
     * @return Shipment
     */
    public function setShipmentDescription (?string $shipmentDescription): Shipment
    {
        $this->shipmentDescription = $shipmentDescription;
        return $this;
    }


    /**
     * @return int
     */
    public function getNumberOfPackages (): ?int
    {
        return $this->numberOfPackages;
    }

    /**
     * @param int $numberOfPackages
     *
     * @return Shipment
     */
    public function setNumberOfPackages (?int $numberOfPackages): Shipment
    {
        $this->numberOfPackages = $numberOfPackages;
        return $this;
    }

    /**
     * @return string
     */
    public function getWeightUnit (): ?string
    {
        return $this->weightUnit;
    }

    /**
     * @param string $weightUnit
     *
     * @return Shipment
     */
    public function setWeightUnit (?string $weightUnit): Shipment
    {
        $this->weightUnit = $weightUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getWeight (): ?float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     *
     * @return Shipment
     */
    public function setWeight (?float $weight): Shipment
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return float
     */
    public function getNetWeight (): ?float
    {
        return $this->netWeight;
    }

    /**
     * @param float $netWeight
     *
     * @return Shipment
     */
    public function setNetWeight (?float $netWeight): Shipment
    {
        $this->netWeight = $netWeight;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPickupDate (): ?DateTime
    {
        return $this->pickupDate;
    }

    public function setPickupDate ($date, $format = null): self
    {
        if ($date === null) {
            return $this;
        }

        if ($format !== null) {
            $this->pickupDate = DateTime::createFromFormat($format, $date);
            return $this;
        }

        if ($date instanceof DateTime) {
            $this->pickupDate = $date;
            return $this;
        }

        $this->pickupDate = new DateTime($date);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getShipmentDate (): ?DateTime
    {
        return $this->shipmentDate;
    }


    public function setShipmentDate ($date, $format = null): self
    {
        if ($date === null) {
            return $this;
        }

        if ($format !== null) {
            $this->shipmentDate = DateTime::createFromFormat($format, $date);
            return $this;
        }

        if ($date instanceof DateTime) {
            $this->shipmentDate = $date;
            return $this;
        }

        $this->shipmentDate = new DateTime($date);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDeliveryDate (): ?DateTime
    {
        return $this->deliveryDate;
    }

    /**
     * @param DateTime $deliveryDate
     *
     * @return Shipment
     */
    public function setDeliveryDate ($date, $format = null): self
    {
        if ($date === null) {
            return $this;
        }

        if ($format !== null) {
            $this->deliveryDate = DateTime::createFromFormat($format, $date);
            return $this;
        }

        if ($date instanceof DateTime) {
            $this->deliveryDate = $date;
            return $this;
        }

        $this->deliveryDate = new DateTime($date);
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnType (): ?string
    {
        return $this->returnType;
    }

    /**
     * @param string $returnType
     *
     * @return Shipment
     */
    public function setReturnType (?string $returnType): Shipment
    {
        $this->returnType = $returnType;
        return $this;
    }

    /**
     * @return AddressInterface
     */
    public function getReturnAddress (): ?AddressInterface
    {
        return $this->returnAddress;
    }

    /**
     * @param AddressInterface $returnAddress
     *
     * @return Shipment
     */
    public function setReturnAddress (AddressInterface $returnAddress): Shipment
    {
        $this->returnAddress = $returnAddress;
        return $this;
    }


}
