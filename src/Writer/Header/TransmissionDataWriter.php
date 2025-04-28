<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Header;

use FatturaElettronicaPhp\FatturaElettronica\Enums\RecipientCode;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;

class TransmissionDataWriter extends AbstractHeaderWriter
{
    protected function performWrite()
    {
        $datiTrasmissione = $this->xml->addChild('DatiTrasmissione');
        $idTrasmittente = $datiTrasmissione->addChild('IdTrasmittente');

        $idTrasmittente->addChild('IdPaese', $this->document->getCountryCode());
        $idTrasmittente->addChild('IdCodice', $this->document->getSenderVatId());

        $datiTrasmissione->addChild('ProgressivoInvio', $this->document->getSendingId());
        $datiTrasmissione->addChild('FormatoTrasmissione', $this->document->getTransmissionFormat()?->value);

        /**
         * Per la PA il CodiceDestinatario è obbligatorio quindi qui non dovrebbe mai essere vuoto
         * (se lo fosse la fattura verrà scartata quindi in quel momento si potrà correggere l'errore)
         */
        $recipientCode = $this->document->getCustomerSdiCode();
        if ($recipientCode === null) {
            $recipientCode = RecipientCode::EMPTY;

            if ($this->document->getCustomer()->getCountryCode() !== 'IT') {
                $recipientCode = RecipientCode::FOREIGN;
            }
        }

        if ($recipientCode instanceof \BackedEnum) {
            $recipientCode = $recipientCode->value;
        }

        $datiTrasmissione->addChild('CodiceDestinatario', SimpleXmlExtended::sanitizeText($recipientCode));

        if ($this->document->getSenderPhone() !== null && $this->document->getSenderEmail() !== null) {
            $contacts = $datiTrasmissione->addChild('ContattiTrasmittente');

            if ($this->document->getSenderPhone() !== null) {
                $contacts->addChild('Telefono', SimpleXmlExtended::sanitizeText($this->document->getSenderPhone()));
            }

            if ($this->document->getSenderEmail() !== null) {
                $contacts->addChild('Email', SimpleXmlExtended::sanitizeText($this->document->getSenderEmail()));
            }
        }

        /* La Casella PEC è da inserire solo se presente e solo se CodiceDestinatario è vuoto*/
        if ($recipientCode === RecipientCode::EMPTY->value && $this->document->getCustomerPec() !== null) {
            $datiTrasmissione->addChild('PECDestinatario', SimpleXmlExtended::sanitizeText($this->document->getCustomerPec()));
        }

        return $this;
    }
}
