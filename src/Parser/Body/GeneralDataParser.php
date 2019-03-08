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

class GeneralDataParser
{
    use XmlUtilities;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /** @var DigitalDocumentInstanceInterface */
    protected $digitalDocymentInstance;

    public function __construct (DigitalDocumentInstanceInterface $digitalDocumentInstance)
    {
        $this->digitalDocymentInstance = $digitalDocumentInstance;
    }

    public function xml (): SimpleXMLElement
    {
        return $this->xml;
    }

    public function parse (SimpleXMLElement $xml): DigitalDocumentInstanceInterface
    {
        $this->xml = $xml;

        $this
            ->extractGeneralData()
            ->extractDeduction()
            ->extractVirtualDuty()
            ->extractFund()
            ->extractDiscount()
            ->extractPurchaseOrder()
            ->extractConventions()
            ->extractReceipt()
            ->extractRelatedInvoices()
            ->extractSal()
            ->extractShippingLabels()
            ->extractTransport()
            ->extractMainInvoice();

        return $this->digitalDocymentInstance;
    }

    public function extractBillableInformationsFrom (SimpleXMLElement $xml): BillableInterface
    {
        $billable = new BillablePerson();

        $title = $this->extractValueFromXmlElement($xml, 'Anagrafica/Nome');
        $billable->setTitle($title);

        $customerName = $this->extractValueFromXml('Anagrafica/Nome');
        $billable->setName($customerName);

        $customerSurname = $this->extractValueFromXml('Anagrafica/Cognome');
        $billable->setSurname($customerSurname);

        $customerOrganization = $this->extractValueFromXml('Anagrafica/Denominazione');
        $billable->setOrganization($customerOrganization);

        $customerFiscalCode = $this->extractValueFromXml('CodiceFiscale');
        $billable->setFiscalCode($customerFiscalCode);

        $value = $this->extractValueFromXml('IdFiscaleIVA/IdPaese');
        $billable->setCountryCode($value);

        $customerVatNumber = $this->extractValueFromXml('IdFiscaleIVA/IdCodice');
        if ($customerVatNumber === null) {
            $customerVatNumber = '';
        }
        $billable->setVatNumber($customerVatNumber);

        return $billable;
    }

    protected function extractAddressInformationFrom (SimpleXMLElement $xml): AddressInterface
    {
        $address = new Address();

        $value = $this->extractValueFromXmlElement($xml, 'Indirizzo');
        $address->setStreet($value);

        $value = $this->extractValueFromXmlElement($xml, 'NumeroCivico');
        $address->setStreetNumber($value);

        $value = $this->extractValueFromXmlElement($xml, 'CAP');
        $address->setZip($value);

        $value = $this->extractValueFromXmlElement($xml, 'Comune');
        $address->setCity($value);

        $value = $this->extractValueFromXmlElement($xml, 'Provincia');
        $address->setState($value);

        $value = $this->extractValueFromXmlElement($xml, 'Nazione');
        $address->setCountryCode($value);

        return $address;
    }

    protected function extractShipmentInformationsFrom (SimpleXMLElement $xml): Shipment
    {
        if ($xml === null) {
            return null;
        }

        $instance = new Shipment();

        $value = $this->extractValueFromXmlElement($xml, 'MezzoTrasporto');
        $instance->setMethod($value);

        $value = $this->extractValueFromXmlElement($xml, 'CausaleTrasporto');
        $instance->setShipmentDescription($value);

        $value = (int)$this->extractValueFromXmlElement($xml, 'NumeroColli');
        $instance->setNumberOfPackages($value);

        $value = $this->extractValueFromXmlElement($xml, 'Descrizione');
        $instance->setDescription($value);

        $value = (int)$this->extractValueFromXmlElement($xml, 'UnitaMisuraPeso');
        $instance->setWeightUnit($value);

        $value = (float)$this->extractValueFromXmlElement($xml, 'PesoLordo');
        $instance->setWeight($value);

        $value = (float)$this->extractValueFromXmlElement($xml, 'PesoNetto');
        $instance->setNetWeight($value);

        $value = $this->extractValueFromXmlElement($xml, 'DataOraRitiro');
        $instance->setPickupDate($value);

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

    protected function extractDigitalDocumentInformations (DigitalDocumentInterface $digitalDocument): DigitalDocumentInterface
    {
        $transmissionFormat = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione');
        if ($transmissionFormat === null) {
            throw new InvalidXmlFile('Transmission Format not found');
        }

        $digitalDocument->setTransmissionFormat($transmissionFormat);

        $countryCode = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese');
        $digitalDocument->setCountryCode($countryCode);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice');
        $digitalDocument->setSenderVatId($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio');
        $digitalDocument->setSendingId($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario');
        $digitalDocument->setCustomerSdiCode($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ContattiTrasmittente/Telefono');
        $digitalDocument->setSenderPhone($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ContattiTrasmittente/Email');
        $digitalDocument->setSenderEmail($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/PECDestinatario');
        $digitalDocument->setCustomerPec($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/SoggettoEmittente');
        $digitalDocument->setEmittingSubject($code);

        return $digitalDocument;
    }

    protected function extractDiscountInformationsFrom ($discount): DiscountInterface
    {
        $discountInstance = new Discount();

        $value = $this->extractValueFromXmlElement($discount, 'Tipo');
        $discountInstance->setType($value);

        $value = $this->extractValueFromXmlElement($discount, 'Percentuale');
        $discountInstance->setPercentage($value);

        $value = $this->extractValueFromXmlElement($discount, 'Importo');
        $discountInstance->setAmount($value);

        return $discountInstance;
    }

    protected function extractRelatedDocumentInformationsFrom ($order): RelatedDocumentInterface
    {
        $instance = new RelatedDocument();

        $value = $this->extractValueFromXmlElement($order, 'RiferimentoNumeroLinea');
        $instance->setLineNumberReference($value);

        $value = $this->extractValueFromXmlElement($order, 'IdDocumento');
        $instance->setDocumentNumber($value);

        $value = $this->extractValueFromXmlElement($order, 'Data');
        $instance->setDocumentDate($value);

        $value = $this->extractValueFromXmlElement($order, 'NumItem');
        $instance->setLineNumber($value);

        $value = $this->extractValueFromXmlElement($order, 'CodiceCommessaConvenzione');
        $instance->setOrderCode($value);

        $value = $this->extractValueFromXmlElement($order, 'CodiceCUP');
        $instance->setCupCode($value);

        $value = $this->extractValueFromXmlElement($order, 'CodiceCIG');
        $instance->setCigCode($value);

        return $instance;
    }

    protected function extractShippingLabelInformationsFrom ($order): ShippingLabel
    {
        $instance = new ShippingLabel();

        $value = $this->extractValueFromXmlElement($order, 'RiferimentoNumeroLinea');
        $instance->setLineNumberReference($value);

        $value = $this->extractValueFromXmlElement($order, 'IdDocumento');
        $instance->setDocumentNumber($value);

        $value = $this->extractValueFromXmlElement($order, 'Data');
        $instance->setDocumentDate($value);

        return $instance;
    }

    protected function extractFundInformationsFrom ($fund): FundInterface
    {
        $fundInstance = new Fund();
        $value = $this->extractValueFromXmlElement($fund, 'TipoCassa');
        $fundInstance->setType($value);

        $value = $this->extractValueFromXmlElement($fund, 'AlCassa');
        $fundInstance->setPercentage($value);

        $value = $this->extractValueFromXmlElement($fund, 'ImportoContributoCassa');
        $fundInstance->setAmount($value);

        $value = $this->extractValueFromXmlElement($fund, 'ImponibileCassa');
        $fundInstance->setSubtotal($value);

        $value = $this->extractValueFromXmlElement($fund, 'AliquotaIVA');
        $fundInstance->setTaxPercentage($value);

        $value = $this->extractValueFromXmlElement($fund, 'Ritenuta');
        $fundInstance->setDeduction($value);

        $value = $this->extractValueFromXmlElement($fund, 'Natura');
        $fundInstance->setVatNature($value);

        $value = $this->extractValueFromXmlElement($fund, 'RiferimentoAmministrazione');
        $fundInstance->setRepresentative($value);

        return $fundInstance;
    }

    protected function extractDeduction (): self
    {
        /**
         * Ritenuta
         */
        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/TipoRitenuta');
        $this->digitalDocymentInstance->setDeductionType($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/ImportoRitenuta');
        $this->digitalDocymentInstance->setDeductionAmount($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/AliquotaRitenuta');
        $this->digitalDocymentInstance->setDeductionPercentage($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/CausalePagamento');
        $this->digitalDocymentInstance->setDeductionDescription($value);

        return $this;
    }

    protected function extractVirtualDuty (): self
    {
        /**
         * Bollo
         */
        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiBollo/BolloVirtuale');
        $this->digitalDocymentInstance->setVirtualDuty($value);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiBollo/ImportoBollo');
        $this->digitalDocymentInstance->setVirtualDutyAmount($value);

        return $this;
    }

    protected function extractFund (): self
    {
        /**
         * Cassa
         */
        $funds = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/DatiCassaPrevidenziale', false);

        foreach ($funds as $fund) {
            $fundInstance = $this->extractFundInformationsFrom($fund);
            $this->digitalDocymentInstance->addFund($fundInstance);
        }

        return $this;
    }

    protected function extractDiscount (): self
    {
        /**
         * Sconto
         */
        $discounts = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/ScontoMaggiorazione', false);
        foreach ($discounts as $discount) {
            $discountInstance = $this->extractDiscountInformationsFrom($discount);
            $this->digitalDocymentInstance->addDiscount($discountInstance);
        }

        return $this;
    }

    protected function extractPurchaseOrder (): self
    {
        /**
         * Ordine di Acquisto
         */
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiOrdineAcquisto', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $this->digitalDocymentInstance->addPurchaseOrder($instance);
        }

        return $this;
    }

    protected function extractConventions (): self
    {
        /**
         * Convenzioni
         */
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiConvenzione', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $this->digitalDocymentInstance->addConvention($instance);
        }
        return $this;
    }

    protected function extractReceipt (): self
    {
        /**
         * Ricezione
         */
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiRicezione', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $this->digitalDocymentInstance->addReceipt($instance);
        }

        return $this;
    }

    protected function extractRelatedInvoices (): self
    {
        /**
         * Fatture Collegate
         */
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiFattureCollegate', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $this->digitalDocymentInstance->addRelatedInvoice($instance);
        }

        return $this;
    }

    protected function extractSal (): self
    {
        /**
         * SAL
         */
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiSal', false);
        foreach ($value as $v) {
            $this->digitalDocymentInstance->addSal($v);
        }
        return $this;
    }

    protected function extractShippingLabels (): self
    {
        /**
         * DDT
         */
        $value = (array)$this->extractValueFromXml('DatiGenerali/DatiDDT', false);
        foreach ($value as $v) {
            $instance = $this->extractShippingLabelInformationsFrom($v);
            $this->digitalDocymentInstance->addShippingLabel($instance);
        }
        return $this;
    }


    protected function extractTransport (): self
    {
        /**
         * Trasporto
         */
        $value = $this->extractValueFromXml('DatiGenerali/DatiTrasporto', false);
        if (count($value) > 0) {
            $value = array_shift($value);
            $instance = $this->extractShipmentInformationsFrom($value);
            $this->digitalDocymentInstance->setShipment($instance);
        }
        return $this;
    }

    protected function extractMainInvoice (): self
    {
        /**
         * Fattura Principale
         */
        $value = $this->extractValueFromXml('DatiGenerali/NumeroFatturaPrincipale');
        $this->digitalDocymentInstance->setMainInvoiceNumber($value);

        $value = $this->extractValueFromXml('DatiGenerali/DataFatturaPrincipale');
        $this->digitalDocymentInstance->setMainInvoiceDate($value);

        return $this;
    }

    protected function extractGeneralData (): self
    {
        /**
         * Dati Generali
         */
        $types = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/TipoDocumento');
        if ($types === null) {
            throw new InvalidXmlFile('<TipoDocumento> not found');
        }
        $type = DocumentType::from($types);

        $datas = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Data');
        if ($datas === null) {
            throw new InvalidXmlFile('<Data> not found');
        }
        $data = new DateTime($datas);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Divisa');
        if ($datas === null) {
            throw new InvalidXmlFile('<Divisa> not found');
        }

        $this->digitalDocymentInstance->setCurrency($value);

        $number = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Numero');
        if ($number === null) {
            throw new InvalidXmlFile('<Numero> not found');
        }

        $descriptions = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Causale', false);
        foreach ($descriptions as $description) {
            $this->digitalDocymentInstance->addDescription($description);
        }

        $documentTotal = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/ImportoTotaleDocumento');
        $rounding = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Arrotondamento');
        $art73 = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Art73');


        $totals = $this->extractValueFromXml('DatiBeniServizi/DatiRiepilogo', false);
        $amount = 0;
        $amountTax = 0;

        foreach ($totals as $total) {
            $totalAmounts = $this->extractValueFromXmlElement($total, 'ImponibileImporto');
            $totalAmountTaxs = $this->extractValueFromXmlElement($total, 'Imposta');

            if ($totalAmounts === null) {
                throw new InvalidXmlFile('<ImponibileImporto> not found');
            }

            if ($totalAmountTaxs === null) {
                throw new InvalidXmlFile('<Imposta> not found');
            }

            $amount += $totalAmounts;
            $amountTax += $totalAmountTaxs;
        }

        $this->digitalDocymentInstance
            ->setDocumentType($type)
            ->setDocumentDate($data)
            ->setDocumentNumber($number)
            ->setAmount($amount)
            ->setAmountTax($amountTax)
            ->setDocumentTotal($documentTotal)
            ->setArt73($art73)
            ->setRounding($rounding);

        return $this;
    }
}
