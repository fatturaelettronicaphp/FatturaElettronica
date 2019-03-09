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
use Weble\FatturaElettronica\Contracts\PaymentDetailsInterface;
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
use Weble\FatturaElettronica\PaymentDetails;
use Weble\FatturaElettronica\PaymentInfo;
use Weble\FatturaElettronica\Product;
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Representative;
use Weble\FatturaElettronica\Shipment;
use Weble\FatturaElettronica\ShippingLabel;
use Weble\FatturaElettronica\Supplier;
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
