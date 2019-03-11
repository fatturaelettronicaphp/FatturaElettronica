<?php

namespace Weble\FatturaElettronica\Writer\Header;

use SimpleXMLElement;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Writer\AbstractWriter;

abstract class AbstractHeaderWriter extends AbstractWriter
{
    /** @var DigitalDocumentInterface */
    protected $document;

    public function write ($document): SimpleXMLElement
    {
        $this->document = $document;

        $this->performWrite();

        return $this->xml;
    }

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
    protected function calculateFiscalData ($idPaese, $codiceFiscale, $idCodice = '')
    {
        if ($idPaese !== 'IT') {
            $idCodice = !empty($idCodice) ? $idCodice : $codiceFiscale;
            $codiceFiscale = '';
        }

        return [
            'idCodice' => $idCodice,
            'codiceFiscale' => $codiceFiscale,
        ];
    }
}
