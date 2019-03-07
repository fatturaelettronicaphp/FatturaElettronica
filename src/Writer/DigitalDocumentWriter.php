<?php

namespace Weble\FatturaElettronica\Writer;

use Weble\FatturaElettronica\Customer;
use Weble\FatturaElettronica\Enums\TaxRegime;
use Weble\FatturaElettronica\Supplier;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentWriterInterface;
use Weble\FatturaElettronica\Enums\RecipientCode;
use Weble\FatturaElettronica\Exceptions\InvalidDocument;
use Weble\FatturaElettronica\Utilities\SimpleXmlExtended;
use SimpleXMLElement;

class DigitalDocumentWriter implements DigitalDocumentWriterInterface
{
    /** @var DigitalDocumentInterface */
    protected $document;

    /** @var SimpleXmlExtended */
    protected $xml;

    /** @var SimpleXmlExtended */
    protected $xmlHeader;

    /** @var SimpleXMLElement */
    protected $xmlBody;

    public function __construct (DigitalDocumentInterface $document)
    {
        $this->document = $document;

        $this->xml = new SimpleXmlExtended('<?xml version="1.0" encoding="UTF-8"?><p:DigitalDocument />', LIBXML_NOERROR);

        $namespaces = [
            'xmlns:xmlns:p' => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2',
            'xmlns:xmlns:ds' => 'http://www.w3.org/2000/09/xmldsig#',
            'xmlns:xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:xsi:schemaLocation' => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd',
            'versione' => (string) $this->document->getTransmissionFormat()
        ];

        foreach ($namespaces as $name => $value) {
            $this->xml->addAttribute($name, $value);
        }

        $this->xmlHeader = $this->xml->addChild('FatturaElettronicaHeader');
        $this->xmlBody = $this->xml->addChild('FatturaElettronicaBody');
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
        $this
            ->addTransmissionData()
            ->addSupplierInformations();

        /*$this->aggiungiCedentePrestatore();

        $this->aggiungiRappresentanteFiscale();

        $this->aggiungiCessionarioCommittente();

        $this->aggiungiTerzoIntermediario();

        $this->aggiungiSoggettoEmittente();

        $this->aggiungiDatiGenerali();

        $this->aggiungiDatiBeniServizi();

        $this->aggiungiDatiPagamento();*/

        return $this;
    }

    protected function addTransmissionData (): DigitalDocumentWriter
    {
        $datiTrasmissione = $this->xmlHeader->addChild('DatiTrasmissione');
        $idTrasmittente = $datiTrasmissione->addChild('IdTrasmittente');

        $idTrasmittente->addChild('IdPaese', $this->document->getCountryCode());
        $idTrasmittente->addChild('IdCodice', $this->document->getSenderVatId());

        $datiTrasmissione->addChild('ProgressivoInvio', $this->document->getSendingId());
        $datiTrasmissione->addChild('FormatoTrasmissione', $this->document->getTransmissionFormat());

        /**
         * Per la PA il CodiceDestinatario è obbligatorio quindi qui non dovrebbe mai essere vuoto
         * (se lo fosse la fattura verrà scartata quindi in quel momento si potrà correggere l'errore)
         */
        $recipientCode = $this->document->getCustomerSdiCode();
        if ($recipientCode === null) {
            $recipientCode = RecipientCode::empty();

            if ($this->document->getCustomer()->getCountryCode() !== 'IT') {
                $recipientCode = RecipientCode::foreign();
            }
        }

        $datiTrasmissione->addChild('CodiceDestinatario', SimpleXmlExtended::sanitizeText((string) $recipientCode));

        /* La Casella PEC è da inserire solo se presente e solo se CodiceDestinatario è vuoto*/
        if ($recipientCode === (string) RecipientCode::empty() && $this->document->getCustomerPec() !== null) {
            $datiTrasmissione->addChild('PECDestinatario', SimpleXmlExtended::sanitizeText($this->document->getCustomerPec()));
        }

        return $this;
    }

    /**
     * Aggiunge i dati del cedente/prestatore
     */
    private function addSupplierInformations (): DigitalDocumentWriter
    {
        $cedentePrestatore = $this->xmlHeader->addChild('CedentePrestatore');
        $datiAnagrafici = $cedentePrestatore->addChild('DatiAnagrafici');

        /** @var Supplier $supplier */
        $supplier = $this->document->getSupplier();
        $idPaese = $supplier->getCountryCode();

        if (empty($idPaese)) {
            throw new InvalidDocument('Invalid empty id paese for cedente prestatore');
        }

        $codiceFiscale = $supplier->getFiscalCode();
        if (empty($codiceFiscale)) {
            throw new InvalidDocument('Invalid empty fiscal code for cedente prestatore');
        }

        $fiscalData = $this->calculateFiscalData($idPaese, $codiceFiscale, $supplier->getVatNumber());

        $idFiscaleIva = $datiAnagrafici->addChild('IdFiscaleIVA');
        $idFiscaleIva->addChild('IdPaese', $idPaese);
        $idFiscaleIva->addChild('IdCodice', SimpleXmlExtended::sanitizeText($fiscalData['idCodice']));

        if (!empty($fiscalData['codiceFiscale'])) {
            $datiAnagrafici->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        $anagrafica = $datiAnagrafici->addChild('Anagrafica');
        if (!empty($supplier->getOrganization())) {
            $anagrafica->addChild('Denominazione', SimpleXmlExtended::sanitizeText($supplier->getOrganization()));
        } else {
            if (!empty($supplier->getName())) {
                $anagrafica->addChild('Nome', SimpleXmlExtended::sanitizeText($supplier->getName()));
            }

            if (!empty($supplier->getSurname())) {
                $anagrafica->addChild('Cognome', SimpleXmlExtended::sanitizeText($supplier->getSurname()));
            }
        }

        if (!empty($supplier->getRegister())) {
            $datiAnagrafici->addChild('AlboProfessionale', SimpleXmlExtended::sanitizeText($supplier->getRegister()));
        }

        if (!empty($supplier->getRegisterState())) {
            $datiAnagrafici->addChild('ProvinciaAlbo', strtoupper(SimpleXmlExtended::sanitizeText($supplier->getRegisterState())));
        }

        if (!empty($supplier->getRegisterNumber())) {
            $datiAnagrafici->addChild('NumeroIscrizioneAlbo', SimpleXmlExtended::sanitizeText($supplier->getRegisterNumber()));
        }

        if (!empty($supplier->getRegisterDate())) {
            $datiAnagrafici->addChild('DataIscrizioneAlbo', $supplier->getRegisterDate()->format(DATE_ISO8601));
        }

        if ($supplier->getTaxRegime()) {
            $datiAnagrafici->addChild('RegimeFiscale', (string) $supplier->getTaxRegime());

        } else {
            $datiAnagrafici->addChild('RegimeFiscale', (string) TaxRegime::default());
        }


        $sede = $cedentePrestatore->addChild('Sede');

        $address = $supplier->getAddress();
        $sede->addChild('Indirizzo', SimpleXmlExtended::sanitizeText($address->getStreet()));

        if (!empty($address->getStreetNumber())) {
            $sede->addChild('NumeroCivico', SimpleXmlExtended::sanitizeText($address->getStreetNumber()));
        }

        $sanitizedCap = preg_replace("/[^0-9]/", '', $address->getZip());
        $sanitizedCap = substr($sanitizedCap, 0, 5);
        $sanitizedCap = str_pad($sanitizedCap, 5, '0', STR_PAD_LEFT);

        $sede->addChild('CAP', $sanitizedCap);
        $sede->addChild('Comune', SimpleXmlExtended::sanitizeText($address->getCity()));

        if (!empty($address->getState())) {
            $sede->addChild('Provincia', strtoupper(SimpleXmlExtended::sanitizeText($address->getState())));
        }
        $sede->addChild('Nazione', $address->getCountryCode());

        if ($supplier->getReaNumber() && $supplier->getReaOffice()) {
            $iscrizioneRea = $cedentePrestatore->addChild('IscrizioneREA');

            $iscrizioneRea->addChild('Ufficio', SimpleXmlExtended::sanitizeText($supplier->getReaOffice()));
            $iscrizioneRea->addChild('NumeroREA', SimpleXmlExtended::sanitizeText($supplier->getReaNumber()));

            if (!empty($supplier->getCapital())) {
                $iscrizioneRea->addChild('CapitaleSociale', $supplier->getCapital());
            }

            if (!empty($supplier->getAssociateType())) {
                $iscrizioneRea->addChild('SocioUnico', SimpleXmlExtended::sanitizeText((string) $supplier->getAssociateType()));
            }

            $iscrizioneRea->addChild('StatoLiquidazione', SimpleXmlExtended::sanitizeText((string) $supplier->getSettlementType()));
        }

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
    private function calculateFiscalData ($idPaese, $codiceFiscale, $idCodice = '')
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
