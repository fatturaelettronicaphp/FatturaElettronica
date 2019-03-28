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
use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentDetailsInterface;
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
use FatturaElettronicaPhp\FatturaElettronica\PaymentDetails;
use FatturaElettronicaPhp\FatturaElettronica\PaymentInfo;
use FatturaElettronicaPhp\FatturaElettronica\Product;
use FatturaElettronicaPhp\FatturaElettronica\RelatedDocument;
use FatturaElettronicaPhp\FatturaElettronica\Representative;
use FatturaElettronicaPhp\FatturaElettronica\Shipment;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Supplier;
use DateTime;
use TypeError;

class PaymentInfoParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $infos = (array)$this->extractValueFromXml('DatiPagamento', false);
        foreach ($infos as $info) {
            $instanceInfo = $this->extractPaymentInfoFrom($info);
            $this->digitalDocymentInstance->addPaymentInformations($instanceInfo);
        }
    }

    protected function extractPaymentDetailsFrom ($detail): PaymentDetailsInterface
    {
        $instanceDetail = new PaymentDetails();

        $value = $this->extractValueFromXmlElement($detail, 'Beneficiario');
        $instanceDetail->setPayee($value);

        $value = $this->extractValueFromXmlElement($detail, 'ModalitaPagamento');
        $instanceDetail->setMethod($value);

        $value = $this->extractValueFromXmlElement($detail, 'DataRiferimentoTerminiPagamento');
        $instanceDetail->setDueDateFrom($value);

        $value = $this->extractValueFromXmlElement($detail, 'GiorniTerminiPagamento');
        $instanceDetail->setDueDays($value);

        $value = $this->extractValueFromXmlElement($detail, 'DataScadenzaPagamento');
        $instanceDetail->setDueDate($value);

        $value = $this->extractValueFromXmlElement($detail, 'ImportoPagamento');
        $instanceDetail->setAmount($value);

        $value = $this->extractValueFromXmlElement($detail, 'CodUfficioPostale');
        $instanceDetail->setPostalOfficeCode($value);

        $value = $this->extractValueFromXmlElement($detail, 'CognomeQuietanzante');
        $instanceDetail->setPayerSurname($value);

        $value = $this->extractValueFromXmlElement($detail, 'NomeQuietanzante');
        $instanceDetail->setPayerName($value);

        $value = $this->extractValueFromXmlElement($detail, 'CFQuietanzante');
        $instanceDetail->setPayerFiscalCode($value);

        $value = $this->extractValueFromXmlElement($detail, 'IBAN');
        $instanceDetail->setIban($value);

        $value = $this->extractValueFromXmlElement($detail, 'ABI');
        $instanceDetail->setAbi($value);

        $value = $this->extractValueFromXmlElement($detail, 'CAB');
        $instanceDetail->setCab($value);

        $value = $this->extractValueFromXmlElement($detail, 'BIC');
        $instanceDetail->setBic($value);

        $value = $this->extractValueFromXmlElement($detail, 'ScontoPagamentoAnticipato');
        $instanceDetail->setEarlyPaymentDiscount($value);

        $value = $this->extractValueFromXmlElement($detail, 'DataLimitePagamentoAnticipato');
        $instanceDetail->setEarlyPaymentDateLimit($value);

        $value = $this->extractValueFromXmlElement($detail, 'PenalitaPagamentiRitardati');
        $instanceDetail->setLatePaymentFee($value);

        $value = $this->extractValueFromXmlElement($detail, 'DataDecorrenzaPenale');
        $instanceDetail->setLatePaymentDateLimit($value);

        $value = $this->extractValueFromXmlElement($detail, 'CodicePagamento');
        $instanceDetail->setPaymentCode($value);

        return $instanceDetail;
    }

    /**
     * @param $info
     * @return PaymentInfo
     */
    protected function extractPaymentInfoFrom ($info): PaymentInfo
    {
        $instanceInfo = new PaymentInfo();

        $value = $this->extractValueFromXmlElement($info, 'CondizioniPagamento');
        $instanceInfo->setTerms($value);

        $details = (array)$this->extractValueFromXmlElement($info, 'DettaglioPagamento', false);
        foreach ($details as $detail) {
            $instanceDetail = $this->extractPaymentDetailsFrom($detail);
            $instanceInfo->addDetails($instanceDetail);
        }
        return $instanceInfo;
    }
}
