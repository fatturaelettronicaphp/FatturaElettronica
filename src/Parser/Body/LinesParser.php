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
use Weble\FatturaElettronica\Contracts\LineInterface;
use Weble\FatturaElettronica\Contracts\OtherDataInterface;
use Weble\FatturaElettronica\Contracts\ProductInterface;
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
use Weble\FatturaElettronica\Line;
use Weble\FatturaElettronica\OtherData;
use Weble\FatturaElettronica\Parser\XmlUtilities;
use Weble\FatturaElettronica\Product;
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Representative;
use Weble\FatturaElettronica\Shipment;
use Weble\FatturaElettronica\ShippingLabel;
use Weble\FatturaElettronica\Supplier;
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
