<?php

namespace Weble\FatturaElettronica\Parser;

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
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Representative;
use Weble\FatturaElettronica\Shipment;
use Weble\FatturaElettronica\ShippingLabel;
use Weble\FatturaElettronica\Supplier;
use DateTime;
use TypeError;

class DigitalDocumentParser implements DigitalDocumentParserInterface
{
    use XmlUtilities;

    /**
     * Nome del file senza estensioni
     * @var string
     */
    protected $fileName;

    /**
     * IL path del file fornito
     * @var string
     */
    protected $filePath;

    /**
     * IL path del file xml
     * @var string
     */
    protected $xmlFilePath;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * @var \Weble\FatturaElettronica\Enums\DocumentFormat
     */
    protected $fileType;

    /**
     * Il path del file p7m se esiste
     * @var string|null
     */
    protected $p7mFilePath;

    /**
     * Costruttore del parser
     *
     * @param string $filePath
     * @param string $extension
     */
    public function __construct ($filePath, $extension = '')
    {
        if (!file_exists($filePath)) {
            throw new InvalidFileNameExtension(sprintf('Fiel does not exist "%s"', $filePath));
        }

        if (empty($extension)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        }

        $this->extractFileNameAndType($filePath, $extension);

        if ($this->fileType->equals(DocumentFormat::p7m())) {
            $this->p7mFilePath = $this->filePath;
            $this->extractP7m();
        }

        if ($this->fileType->equals(DocumentFormat::xml())) {
            $this->xmlFilePath = $this->filePath;
        }
    }

    public function parse (DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface
    {
        if ($digitalDocument === null) {
            $digitalDocument = new DigitalDocument();
        }

        $simpleXml = $this->xml();

        $headerParser = new DigitalDocumentHeaderParser($simpleXml);
        $digitalDocument = $headerParser->parse($digitalDocument);

        foreach ($simpleXml->xpath('//FatturaElettronicaBody') as $body) {
            $bodyParser = new DigitalDocumentBodyParser($body);
            $bodyInstance = $bodyParser->parse();
            $digitalDocument->addDigitalDocumentInstance($bodyInstance);
        }

        return $digitalDocument;
    }

    protected function extractP7m (): void
    {
        try {
            $this->xmlFilePath = $this->extractP7mToXml($this->p7mFilePath);
        } catch (InvalidP7MFile $e) {
            $p7mTmpFilePath = tempnam(sys_get_temp_dir(), 'fattura_elettronica_') . '.xml.p7m';
            file_put_contents($p7mTmpFilePath, base64_decode(file_get_contents($this->p7mFilePath)));

            $this->xmlFilePath = $this->extractP7mToXml($p7mTmpFilePath);
        }
    }

    protected function extractP7mToXml ($p7mFilePath): string
    {
        $xmlPath = tempnam(sys_get_temp_dir(), 'fattura_elettronica_') . '.xml';

        $output = [];
        $exitCode = 0;
        exec(sprintf('openssl smime -verify -noverify -nosigs -in %s -inform DER -out %s', $p7mFilePath, $xmlPath), $output, $exitCode);

        if ($exitCode !== 0) {
            throw new InvalidP7MFile('Invalid p7m file opening for file ' . $this->p7mFilePath);
        }

        return $xmlPath;
    }

    public function xml (): SimpleXMLElement
    {
        if ($this->xml !== null) {
            return $this->xml;
        }

        libxml_use_internal_errors(true);
        $simpleXml = simplexml_load_string(file_get_contents($this->xmlFilePath()), 'SimpleXMLElement', LIBXML_NOERROR + LIBXML_NOWARNING);

        if (!$simpleXml) {
            throw new InvalidXmlFile();
        }

        $this->xml = $simpleXml;

        return $this->xml;
    }

    public function originalFilename ()
    {
        return $this->fileName;
    }

    public function xmlFilePath ()
    {
        return $this->xmlFilePath;
    }

    public function p7mFilePath ()
    {
        return $this->p7mFilePath;
    }

    protected function extractFileNameAndType ($filePath, $extension): void
    {
        // Split extension and file name
        $extension = strtolower($extension);
        $fileName = pathinfo($filePath, PATHINFO_BASENAME);
        $fileNameParts = explode(".", $fileName);
        $this->fileName = array_shift($fileNameParts);

        try {
            $this->fileType = DocumentFormat::from($extension);
            $this->filePath = $filePath;
        } catch (TypeError $e) {
            throw new InvalidFileNameExtension(sprintf('Invalid file extension "%s"', $extension));
        }
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

    protected function extractRowInformationsFrom (SimpleXMLElement $body): DigitalDocumentInstanceInterface
    {
        $digitalDocumentInstance = new DigitalDocumentInstance();

        /**
         * Dati Generali
         */
        $types = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/TipoDocumento');
        if ($types === null) {
            throw new InvalidXmlFile('<TipoDocumento> not found');
        }
        $type = DocumentType::from($types);

        $datas = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/Data');
        if ($datas === null) {
            throw new InvalidXmlFile('<Data> not found');
        }
        $data = new DateTime($datas);

        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/Divisa');
        if ($datas === null) {
            throw new InvalidXmlFile('<Divisa> not found');
        }
        $digitalDocumentInstance->setCurrency($value);

        $number = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/Numero');
        if ($number === null) {
            throw new InvalidXmlFile('<Numero> not found');
        }

        $descriptions = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/Causale', false);
        foreach ($descriptions as $description) {
            $digitalDocumentInstance->addDescription($description);
        }


        $documentTotal = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/ImportoTotaleDocumento');
        $rounding = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/Arrotondamento');
        $art73 = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/Art73');

        /**
         * Ritenuta
         */
        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/TipoRitenuta');
        $digitalDocumentInstance->setDeductionType($value);

        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/ImportoRitenuta');
        $digitalDocumentInstance->setDeductionAmount($value);

        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/AliquotaRitenuta');
        $digitalDocumentInstance->setDeductionPercentage($value);

        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/DatiRitenuta/CausalePagamento');
        $digitalDocumentInstance->setDeductionDescription($value);

        /**
         * Bollo
         */
        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/DatiBollo/BolloVirtuale');
        $digitalDocumentInstance->setVirtualDuty($value);

        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/DatiBollo/ImportoBollo');
        $digitalDocumentInstance->setVirtualDutyAmount($value);

        /**
         * Cassa
         */
        $funds = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/DatiCassaPrevidenziale', false);

        foreach ($funds as $fund) {
            $fundInstance = $this->extractFundInformationsFrom($fund);
            $digitalDocumentInstance->addFund($fundInstance);
        }

        /**
         * Sconto
         */
        $discounts = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiGeneraliDocumento/ScontoMaggiorazione', false);
        foreach ($discounts as $discount) {
            $discountInstance = $this->extractDiscountInformationsFrom($discount);
            $digitalDocumentInstance->addDiscount($discountInstance);
        }

        /**
         * Ordine di Acquisto
         */
        $value = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiOrdineAcquisto', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $digitalDocumentInstance->addPurchaseOrder($instance);
        }

        /**
         * Convenzioni
         */
        $value = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiConvenzione', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $digitalDocumentInstance->addConvention($instance);
        }

        /**
         * Ricezione
         */
        $value = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiRicezione', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $digitalDocumentInstance->addReceipt($instance);
        }

        /**
         * Fatture Collegate
         */
        $value = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiFattureCollegate', false);
        foreach ($value as $v) {
            $instance = $this->extractRelatedDocumentInformationsFrom($v);
            $digitalDocumentInstance->addRelatedInvoice($instance);
        }

        /**
         * SAL
         */
        $value = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiSal', false);
        foreach ($value as $v) {
            $digitalDocumentInstance->addSal($v);
        }

        /**
         * DDT
         */
        $value = (array)$this->extractValueFromXmlElement($body, 'DatiGenerali/DatiDDT', false);
        foreach ($value as $v) {
            $instance = $this->extractShippingLabelInformationsFrom($v);
            $digitalDocumentInstance->addShippingLabel($instance);
        }

        /**
         * Trasporto
         */
        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DatiTrasporto', false);
        if (count($value) > 0) {
            $value = array_shift($value);
            $instance = $this->extractShipmentInformationsFrom($value);
            $digitalDocumentInstance->setShipment($instance);
        }


        /**
         * Fattura Principale
         */
        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/NumeroFatturaPrincipale');
        $digitalDocumentInstance->setMainInvoiceNumber($value);

        $value = $this->extractValueFromXmlElement($body, 'DatiGenerali/DataFatturaPrincipale');
        $digitalDocumentInstance->setMainInvoiceDate($value);


        $totals = $this->extractValueFromXmlElement($body, 'DatiBeniServizi/DatiRiepilogo', false);
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

        $digitalDocumentInstance
            ->setDocumentType($type)
            ->setDocumentDate($data)
            ->setDocumentNumber($number)
            ->setAmount($amount)
            ->setAmountTax($amountTax)
            ->setDocumentTotal($documentTotal)
            ->setArt73($art73)
            ->setRounding($rounding);

        return $digitalDocumentInstance;
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
}
