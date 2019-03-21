<?php

namespace Weble\FatturaElettronica\Parser\Body;

use Weble\FatturaElettronica\Address;
use Weble\FatturaElettronica\Billable;
use Weble\FatturaElettronica\BillablePerson;
use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentParserInterface;
use Weble\FatturaElettronica\Contracts\DiscountInterface;
use Weble\FatturaElettronica\Contracts\FundInterface;
use Weble\FatturaElettronica\Contracts\RelatedDocumentInterface;
use Weble\FatturaElettronica\Customer;
use Weble\FatturaElettronica\DigitalDocument;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use Weble\FatturaElettronica\Discount;
use Weble\FatturaElettronica\Enums\DocumentFormat;
use Weble\FatturaElettronica\Enums\DocumentType;
use Weble\FatturaElettronica\Exceptions\InvalidFileNameExtension;
use Weble\FatturaElettronica\Exceptions\InvalidP7MFile;
use SimpleXMLElement;
use Weble\FatturaElettronica\Exceptions\InvalidXmlFile;
use Weble\FatturaElettronica\Fund;
use Weble\FatturaElettronica\Parser\XmlUtilities;
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Representative;
use Weble\FatturaElettronica\Shipment;
use Weble\FatturaElettronica\ShippingLabel;
use Weble\FatturaElettronica\Supplier;
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
