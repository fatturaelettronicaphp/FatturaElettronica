<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\PaymentDetailsInterface;
use FatturaElettronicaPhp\FatturaElettronica\PaymentDetails;
use FatturaElettronicaPhp\FatturaElettronica\PaymentInfo;

class PaymentInfoParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $infos = (array)$this->extractValueFromXml('DatiPagamento', false);
        foreach ($infos as $info) {
            $instanceInfo = $this->extractPaymentInfoFrom($info);
            $this->digitalDocymentInstance->addPaymentInformations($instanceInfo);
        }
    }

    protected function extractPaymentDetailsFrom($detail): PaymentDetailsInterface
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
    protected function extractPaymentInfoFrom($info): PaymentInfo
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
