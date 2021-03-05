<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer;

use SimpleXMLElement;

abstract class AbstractWriter
{
    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    abstract public function write($document);

    abstract protected function performWrite();

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
        if ($idPaese !== 'IT') {
            $idCodice      = ! empty($idCodice) ? $idCodice : $codiceFiscale;
            $codiceFiscale = '';
        }

        return [
            'idCodice'      => $idCodice,
            'codiceFiscale' => $codiceFiscale,
        ];
    }
}
