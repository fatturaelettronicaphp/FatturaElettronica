<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Header;

use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidXmlFile;

class TransmissionDataParser extends AbstractHeaderParser
{
    protected function performParsing()
    {
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

        return $this->document;
    }
}
