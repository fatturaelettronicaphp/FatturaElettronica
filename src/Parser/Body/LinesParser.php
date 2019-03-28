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
use FatturaElettronicaPhp\FatturaElettronica\Contracts\LineInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\OtherDataInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\ProductInterface;
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
use FatturaElettronicaPhp\FatturaElettronica\Line;
use FatturaElettronicaPhp\FatturaElettronica\OtherData;
use FatturaElettronicaPhp\FatturaElettronica\Parser\XmlUtilities;
use FatturaElettronicaPhp\FatturaElettronica\Product;
use FatturaElettronicaPhp\FatturaElettronica\RelatedDocument;
use FatturaElettronicaPhp\FatturaElettronica\Representative;
use FatturaElettronicaPhp\FatturaElettronica\Shipment;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use DateTime;
use TypeError;

class LinesParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $rows = (array) $this->extractValueFromXml('DatiBeniServizi/DettaglioLinee', false);
        foreach ($rows as $row) {
            $line = $this->extractProductInformationsFrom($row);
            $this->digitalDocymentInstance->addLine($line);
        }

    }

    protected function extractProductInformationsFrom ($xml): LineInterface
    {
        $instance = new Line();
        $value = $this->extractValueFromXmlElement($xml, 'NumeroLinea');
        $instance->setNumber( (int) $value);

        $value = $this->extractValueFromXmlElement($xml, 'TipoCessionePrestazione');
        $instance->setTipoCessazionePrestazione( $value);

        $value = $this->extractValueFromXmlElement($xml, 'Descrizione');
        $instance->setDescription( $value);

        $value = $this->extractValueFromXmlElement($xml, 'Quantita');
        $instance->setQuantity((float) $value);

        $value = $this->extractValueFromXmlElement($xml, 'UnitaMisura');
        $instance->setUnit( $value);

        $value = $this->extractValueFromXmlElement($xml, 'DataInizioPeriodo');
        $instance->setStartDate( $value);

        $value = $this->extractValueFromXmlElement($xml, 'DataFinePeriodo');
        $instance->setEndDate( $value);

        $value = $this->extractValueFromXmlElement($xml, 'PrezzoUnitario');
        $instance->setUnitPrice( $value);

        $value = $this->extractValueFromXmlElement($xml, 'PrezzoTotale');
        $instance->setTotal( $value);

        $value = $this->extractValueFromXmlElement($xml, 'AliquotaIVA');
        $instance->setTaxPercentage( $value);

        $value = $this->extractValueFromXmlElement($xml, 'Natura');
        $instance->setVatNature( $value);

        $products = (array) $this->extractValueFromXmlElement($xml, 'CodiceArticolo', false);
        foreach ($products as $product) {
            $value = $this->extractProductCodeInformationsFrom($product);
            $instance->addProduct($value);
        }

        $discounts = (array) $this->extractValueFromXmlElement($xml, 'ScontoMaggiorazione', false);
        foreach ($discounts as $discount) {
            $value = $this->extractDiscountInformationsFrom($discount);
            $instance->addDiscount($value);
        }

        $datas = (array) $this->extractValueFromXmlElement($xml, 'AltriDatiGestionali', false);
        foreach ($datas as $data) {
            $value = $this->extractOtherDataFrom($data);
            $instance->addOtherData($value);
        }

        return $instance;
    }

    protected function extractProductCodeInformationsFrom ($xml): ProductInterface
    {
        $instance = new Product();

        $value = $this->extractValueFromXmlElement($xml, 'CodiceTipo');
        $instance->setCodeType(trim($value));

        $value = $this->extractValueFromXmlElement($xml, 'CodiceValore');
        $instance->setCode(trim($value));

        return $instance;
    }

    protected function extractDiscountInformationsFrom ($xml): DiscountInterface
    {
        $instance = new Discount();

        $value = $this->extractValueFromXmlElement($xml, 'Tipo');
        $instance->setType($value);

        $value = $this->extractValueFromXmlElement($xml, 'Percentuale');
        $instance->setPercentage((float) $value);

        $value = $this->extractValueFromXmlElement($xml, 'Importo');
        $instance->setAmount((float) $value);

        return $instance;
    }

    protected function extractOtherDataFrom ($xml): OtherDataInterface
    {
        $instance = new OtherData();

        $value = $this->extractValueFromXmlElement($xml, 'TipoDato');
        $instance->setType($value);

        $value = $this->extractValueFromXmlElement($xml, 'RiferimentoTesto');
        $instance->setText($value);

        $value = $this->extractValueFromXmlElement($xml, 'RiferimentoNumero');
        $instance->setNumber( $value);

        $value = $this->extractValueFromXmlElement($xml, 'RiferimentoData');
        $instance->setDate($value);

        return $instance;
    }
}
