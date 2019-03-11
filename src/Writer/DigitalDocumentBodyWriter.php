<?php

namespace Weble\FatturaElettronica\Writer;

use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\RelatedDocumentInterface;
use Weble\FatturaElettronica\Customer;
use Weble\FatturaElettronica\Enums\TaxRegime;
use Weble\FatturaElettronica\RelatedDocument;
use Weble\FatturaElettronica\Supplier;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentWriterInterface;
use Weble\FatturaElettronica\Enums\RecipientCode;
use Weble\FatturaElettronica\Exceptions\InvalidDocument;
use Weble\FatturaElettronica\Utilities\SimpleXmlExtended;
use SimpleXMLElement;

class DigitalDocumentBodyWriter implements DigitalDocumentWriterInterface
{
    /** @var DigitalDocumentInterface */
    protected $document;

    /** @var SimpleXMLElement */
    protected $xmlBody;

    /** @var SimpleXMLElement */
    protected $xml;

    public function __construct (DigitalDocumentInstanceInterface $document, SimpleXMLElement $root)
    {
        $this->document = $document;
        $this->xml = $root;
        $this->xmlBody = $this->xml->addChild('FatturazioneElettronicaBody');
    }

    public function xml (): SimpleXMLElement
    {
        return $this->xml;
    }

    public function write ($filePath): bool
    {
        return true;
    }


    public function generate (): DigitalDocumentWriterInterface
    {



        /**
         * $this->aggiungiDatiGenerali();
         *
         * $this->aggiungiDatiBeniServizi();
         *
         * $this->aggiungiDatiPagamento();*/

        return $this;
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
