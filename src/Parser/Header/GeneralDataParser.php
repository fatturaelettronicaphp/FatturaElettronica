<?php

namespace Weble\FatturaElettronica\Parser\Header;


use Weble\FatturaElettronica\Exceptions\InvalidXmlFile;

class GeneralDataParser extends AbstractHeaderParser
{
    protected function performParsing ()
    {
        $transmissionFormat = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione');
        if ($transmissionFormat === null) {
            throw new InvalidXmlFile('Transmission Format not found');
        }

        $this->document->setTransmissionFormat($transmissionFormat);

        $countryCode = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese');
        $this->document->setCountryCode($countryCode);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice');
        $this->document->setSenderVatId($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio');
        $this->document->setSendingId($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario');
        $this->document->setCustomerSdiCode($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ContattiTrasmittente/Telefono');
        $this->document->setSenderPhone($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/ContattiTrasmittente/Email');
        $this->document->setSenderEmail($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/DatiTrasmissione/PECDestinatario');
        $this->document->setCustomerPec($code);

        $code = $this->extractValueFromXml('//FatturaElettronicaHeader/SoggettoEmittente');
        $this->document->setEmittingSubject($code);

        return $this->document;
    }
}
