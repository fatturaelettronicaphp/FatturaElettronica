<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Address;
use FatturaElettronicaPhp\FatturaElettronica\Billable;
use FatturaElettronicaPhp\FatturaElettronica\BillablePerson;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AddressInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\FundInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\RelatedDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Customer;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance;
use FatturaElettronicaPhp\FatturaElettronica\Discount;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentFormat;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentType;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidFileNameExtension;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidP7MFile;
use SimpleXMLElement;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidXmlFile;
use FatturaElettronicaPhp\FatturaElettronica\Fund;
use FatturaElettronicaPhp\FatturaElettronica\Parser\XmlUtilities;
use FatturaElettronicaPhp\FatturaElettronica\RelatedDocument;
use FatturaElettronicaPhp\FatturaElettronica\Representative;
use FatturaElettronicaPhp\FatturaElettronica\Shipment;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use DateTime;
use TypeError;

class ShipmentInformationsParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $value = $this->extractValueFromXml('DatiGenerali/DatiTrasporto', false);
        if ($value !== null && count($value) > 0) {
            $value = array_shift($value);

            $instance = $this->extractShipmentInformationsFrom($value);

            if ($instance !== null ) {
                $this->digitalDocymentInstance->setShipment($instance);
            }
        }
    }

    protected function extractShipmentInformationsFrom (SimpleXMLElement $xml): ?Shipment
    {
        if ($xml === null) {
            return null;
        }

        $instance = new Shipment();

        $value = $this->extractValueFromXmlElement($xml, 'MezzoTrasporto');
        $instance->setMethod($value);

        $value = $this->extractValueFromXmlElement($xml, 'CausaleTrasporto');
        $instance->setShipmentDescription($value);

        $value = $this->extractValueFromXmlElement($xml, 'NumeroColli');
        $instance->setNumberOfPackages($value);

        $value = $this->extractValueFromXmlElement($xml, 'Descrizione');
        $instance->setDescription($value);

        $value = $this->extractValueFromXmlElement($xml, 'UnitaMisuraPeso');
        $instance->setWeightUnit($value);

        $value = $this->extractValueFromXmlElement($xml, 'PesoLordo');
        $instance->setWeight($value);

        $value = $this->extractValueFromXmlElement($xml, 'PesoNetto');
        $instance->setNetWeight($value);

        $value = $this->extractValueFromXmlElement($xml, 'DataOraRitiro');
        $instance->setPickupDate($value);

        $value = $this->extractValueFromXmlElement($xml, 'DataOraConsegna');
        $instance->setDeliveryDate($value);

        $value = $this->extractValueFromXmlElement($xml, 'DataInizioTrasporto');
        $instance->setShipmentDate($value);

        $value = $this->extractValueFromXmlElement($xml, 'TipoResa');
        $instance->setReturnType($value);

        $value = $this->extractValueFromXmlElement($xml, 'IndirizzoResa', false);
        if ($value !== null) {
            $value = $this->extractAddressInformationFrom($value);
            $instance->setReturnAddress($value);
        }

        $value = $this->extractValueFromXmlElement($xml, 'DatiAnagraficiVettore', false);
        if ($value !== null && count($value) > 0) {
            $value = array_shift($value);
            if ($value !== null) {
                $value = $this->extractBillableInformationsFrom($value);
                $instance->setShipper($value);
            }
        }

        return $instance;
    }
}
