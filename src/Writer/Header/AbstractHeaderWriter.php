<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Header;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Writer\AbstractWriter;
use SimpleXMLElement;

abstract class AbstractHeaderWriter extends AbstractWriter
{
    /** @var DigitalDocumentInterface */
    protected $document;

    public function write($document): SimpleXMLElement
    {
        $this->document = $document;

        $this->performWrite();

        return $this->xml;
    }

    /**
     * Ricalcola i dati fiscali di un anagrafica utente sulla base della nazione
     *
     * @param string $idPaese
     * @param string $codiceFiscale
     * @param string $idCodice
     *
     * @return array
     * Anagrafica italiana:
     *  - IdCodice = partita iva se indicata
     *  - CodiceFiscale = codice fiscale
     * Anagrafica estera:
     *  - IdCodice = partita iva se indicata, altrimenti codice fiscale
     *  - CodiceFiscale = vuoto
     */
    protected function calculateFiscalData($idPaese, $codiceFiscale, $idCodice = '')
    {
        if ($idPaese !== null && $idPaese !== 'IT') {
            $idCodice      = ! empty($idCodice) ? $idCodice : $codiceFiscale;
            $codiceFiscale = '';
        }

        return [
            'idCodice'      => $idCodice,
            'codiceFiscale' => $codiceFiscale,
        ];
    }
}
