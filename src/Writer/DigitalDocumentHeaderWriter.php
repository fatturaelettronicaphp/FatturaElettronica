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

class DigitalDocumentHeaderWriter implements DigitalDocumentWriterInterface
{
    /** @var DigitalDocumentInterface */
    protected $document;

    /** @var SimpleXmlExtended */
    protected $xmlHeader;

    /** @var SimpleXMLElement $xml */
    protected $xml;

    public function __construct (DigitalDocumentInterface $document, SimpleXMLElement $root)
    {
        $this->document = $document;

        $this->xml = $root;
        $this->xmlHeader = $this->xml->addChild('FatturaElettronicaHeader');
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
            ->addSupplierInformations()
            ->addRepresentitiveData()
            ->addCustomerInformations()
            ->addIntermediaryInformations()
            ->addEmittingSubject();

        /**
         * $this->aggiungiDatiGenerali();
         *
         * $this->aggiungiDatiBeniServizi();
         *
         * $this->aggiungiDatiPagamento();*/

        return $this;
    }

    protected function addTransmissionData (): self
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

        $datiTrasmissione->addChild('CodiceDestinatario', SimpleXmlExtended::sanitizeText((string)$recipientCode));

        /* La Casella PEC è da inserire solo se presente e solo se CodiceDestinatario è vuoto*/
        if ($recipientCode === (string)RecipientCode::empty() && $this->document->getCustomerPec() !== null) {
            $datiTrasmissione->addChild('PECDestinatario', SimpleXmlExtended::sanitizeText($this->document->getCustomerPec()));
        }

        return $this;
    }

    /**
     * Aggiunge i dati del cedente/prestatore
     */
    protected function addSupplierInformations (): self
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
            $datiAnagrafici->addChild('RegimeFiscale', (string)$supplier->getTaxRegime());

        } else {
            $datiAnagrafici->addChild('RegimeFiscale', (string)TaxRegime::default());
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
                $iscrizioneRea->addChild('SocioUnico', SimpleXmlExtended::sanitizeText((string)$supplier->getAssociateType()));
            }

            $iscrizioneRea->addChild('StatoLiquidazione', SimpleXmlExtended::sanitizeText((string)$supplier->getSettlementType()));
        }

        return $this;
    }

    /**
     * Aggiunge i dati del rappresentante fiscale
     */
    protected function addRepresentitiveData (): self
    {
        $documentPerson = $this->document->getIntermediary();

        if ($documentPerson === null) {
            return $this;
        }

        $rappresentanteFiscale = $this->xmlHeader->addChild('RappresentateFiscale');
        $datiAnagrafici = $rappresentanteFiscale->addChild('DatiAnagrafici');

        $idPaese = $documentPerson->getCountryCode();
        if (empty($idPaese)) {
            throw new InvalidDocument('Invalid empty id paese for rappresentante fiscale');
        }

        $codiceFiscale = $documentPerson->getFiscalCode();
        if (empty($codiceFiscale)) {
            throw new InvalidDocument('Invalid empty fiscal code for rappresentante fiscale');
        }

        $fiscalData = $this->calculateFiscalData($idPaese, $codiceFiscale, $documentPerson->getVatNumber());

        $idFiscaleIva = $datiAnagrafici->addChild('IdFiscaleIVA');
        $idFiscaleIva->addChild('IdPaese', $idPaese);
        $idFiscaleIva->addChild('IdCodice', $fiscalData['idCodice']);

        if (!empty($fiscalData['codiceFiscale'])) {
            $datiAnagrafici->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        $anagrafica = $datiAnagrafici->addChild('Anagrafica');

        if (!empty($documentPerson->getOrganization())) {
            $anagrafica->addChild('Denominazione', SimpleXmlExtended::sanitizeText($documentPerson->getOrganization()));

        } else {
            if (!empty($documentPerson->getName())) {
                $anagrafica->addChild('Nome', SimpleXmlExtended::sanitizeText($documentPerson->getName()));
            }

            if (!empty($documentPerson->getSurname())) {
                $anagrafica->addChild('Cognome', SimpleXmlExtended::sanitizeText($documentPerson->getSurname()));
            }
        }

        return $this;
    }

    protected function addCustomerInformations (): self
    {
        $cessionarioCommittente = $this->xmlHeader->addChild('CessionarioCommittente');
        $datiAnagrafici = $cessionarioCommittente->addChild('DatiAnagrafici');

        /** @var Customer $customer */
        $customer = $this->document->getCustomer();
        $idPaese = $customer->getCountryCode();

        if (empty($idPaese)) {
            throw new InvalidDocument('Invalid empty id paese for cessionario committente');
        }

        $codiceFiscale = $customer->getFiscalCode();
        if (empty($codiceFiscale) && empty($customer->getVatNumber())) {
            throw new InvalidDocument('Invalid empty fiscal code for cessionario committente');
        }

        $fiscalData = $this->calculateFiscalData($idPaese, $codiceFiscale, $customer->getVatNumber());

        if (!empty($fiscalData['idCodice'])) {
            $idFiscaleIva = $datiAnagrafici->addChild('IdFiscaleIVA');
            $idFiscaleIva->addChild('IdPaese', $idPaese);
            $idFiscaleIva->addChild('IdCodice', SimpleXmlExtended::sanitizeText($fiscalData['idCodice']));
        }

        if (!empty($fiscalData['codiceFiscale'])) {
            $datiAnagrafici->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        $anagrafica = $datiAnagrafici->addChild('Anagrafica');

        if (!empty($customer->getOrganization())) {
            $anagrafica->addChild('Denominazione', SimpleXmlExtended::sanitizeText($customer->getOrganization()));

        } else {
            if (!empty($customer->getName())) {
                $anagrafica->addChild('Nome', SimpleXmlExtended::sanitizeText($customer->getName()));
            }

            if (!empty($customer->getSurname())) {
                $anagrafica->addChild('Cognome', SimpleXmlExtended::sanitizeText($customer->getSurname()));
            }
        }

        $sede = $cessionarioCommittente->addChild('Sede');
        $address = $customer->getAddress();
        $sede->addChild('Indirizzo', SimpleXmlExtended::sanitizeText($address->getStreet()));

        if (!empty($address->getStreetNumber())) {
            $sede->addChild('NumeroCivico', SimpleXmlExtended::sanitizeText($address->getStreetNumber()));
        }

        $sanitizedCap = preg_replace("/[^0-9]/", '', $address->getZip());
        $sanitizedCap = substr($sanitizedCap, 0, 5);
        $sanitizedCap = str_pad($sanitizedCap, 5, '0', STR_PAD_LEFT);

        $sede->addChild('CAP', $sanitizedCap);
        $sede->addChild('Comune', SimpleXmlExtended::sanitizeText($address->getCity()));

        if (!empty($address->getState()) and ($address->getCountryCode() == 'IT')) {
            $sede->addChild('Provincia', strtoupper(SimpleXmlExtended::sanitizeText($address->getState())));
        }

        $sede->addChild('Nazione', $address->getCountryCode());

        $address = $customer->getForeignFixedAddress();
        if ($address) {
            $stabileOrganizzazione = $cessionarioCommittente->addChild('StabileOrganizzazione');

            $stabileOrganizzazione->addChild('Indirizzo', SimpleXmlExtended::sanitizeText($address->getStreet()));

            if (!empty($address->getStreetNumber())) {
                $stabileOrganizzazione->addChild('NumeroCivico', SimpleXmlExtended::sanitizeText($address->getStreetNumber()));
            }

            $sanitizedCap = preg_replace("/[^0-9]/", '', $address->getZip());
            $sanitizedCap = substr($sanitizedCap, 0, 5);
            $sanitizedCap = str_pad($sanitizedCap, 5, '0', STR_PAD_LEFT);

            $stabileOrganizzazione->addChild('CAP', $sanitizedCap);

            $stabileOrganizzazione->addChild('Comune', SimpleXmlExtended::sanitizeText($address->getCity()));

            if (!empty($address->getState()) and ($address->getCountryCode() == 'IT')) {
                $stabileOrganizzazione->addChild('Provincia', strtoupper(SimpleXmlExtended::sanitizeText($address->getState())));
            }

            $stabileOrganizzazione->addChild('Nazione', $address->getCountryCode());
        }

        $personAgent = $customer->getRepresentative();

        if ($personAgent) {
            $rappresentanteFiscale = $cessionarioCommittente->addChild('RappresentanteFiscale');

            $idFiscaleIva = $rappresentanteFiscale->addChild('IdFiscaleIVA');
            $idFiscaleIva->addChild('IdPaese', $personAgent->getCountryCode());
            $idFiscaleIva->addChild('IdCodice', SimpleXmlExtended::sanitizeText($personAgent->getVatNumber()));

            if (!empty($personAgent->getOrganization())) {
                $rappresentanteFiscale->addChild('Denominazione', SimpleXmlExtended::sanitizeText($personAgent->getOrganization()));

            } else {
                if (!empty($personAgent->getName())) {
                    $rappresentanteFiscale->addChild('Nome', SimpleXmlExtended::sanitizeText($personAgent->getName()));
                }

                if (!empty($personAgent->getSurname())) {
                    $rappresentanteFiscale->addChild('Cognome', SimpleXmlExtended::sanitizeText($personAgent->getSurname()));
                }
            }
        }

        return $this;
    }

    protected function addIntermediaryInformations (): self
    {
        $intermediary = $this->document->getIntermediary();

        if (!$intermediary) {
            return $this;
        }

        $intermediario = $this->xmlHeader->addChild('TerzoIntermediarioOSoggettoEmittente');
        $datiAnagrafici = $intermediario->addChild('DatiAnagrafici');

        $idPaese = $intermediary->getCountryCode();
        if (empty($idPaese)) {
            throw new InvalidDocument('Invalid empty id paese for terzo intermediario');
        }

        $codiceFiscale = $intermediary->getFiscalCode();
        if (empty($codiceFiscale)) {
            throw new InvalidDocument('Invalid empty fiscal code for terzo intermediario');
        }

        $fiscalData = $this->calculateFiscalData($idPaese, $codiceFiscale, $intermediary->getVatNumber());

        if (!empty($fiscalData['idCodice'])) {
            $idFiscaleIva = $datiAnagrafici->addChild('IdFiscaleIVA');
            $idFiscaleIva->addChild('IdPaese', $idPaese);
            $idFiscaleIva->addChild('IdCodice', SimpleXmlExtended::sanitizeText($fiscalData['idCodice']));
        }

        if (!empty($fiscalData['codiceFiscale'])) {
            $datiAnagrafici->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        $anagrafica = $datiAnagrafici->addChild('Anagrafica');

        if (!empty($intermediary->getOrganization())) {
            $anagrafica->addChild('Denominazione', SimpleXmlExtended::sanitizeText($intermediary->getOrganization()));

        } else {
            if (!empty($intermediary->getName())) {
                $anagrafica->addChild('Nome', SimpleXmlExtended::sanitizeText($intermediary->getName()));
            }

            if (!empty($intermediary->getSurname())) {
                $anagrafica->addChild('Cognome', SimpleXmlExtended::sanitizeText($intermediary->getSurname()));
            }
        }
    }

    protected function addEmittingSubject (): self
    {
        if (!empty($this->document->getEmittingSubject())) {
            $this->xmlHeader->addChild('SoggettoEmittente', $this->document->getEmittingSubject());
        }

        return $this;
    }

    protected function addGeneralData (DigitalDocumentInstanceInterface $instance)
    {
        $generalData = $this->xmlBody->addChild('DatiGenerali');

        $documentGeneralData = $generalData->addChild('DatiGeneraliDocumento');
        $documentGeneralData->addChild('TipoDocumento', (string)$instance->getDocumentType());
        $documentGeneralData->addChild('Divisa', $instance->getCurrency());
        $documentGeneralData->addChild('Data', $instance->getDocumentDate()->format(DATE_ISO8601));
        $documentGeneralData->addChild('Numero', $instance->getDocumentNumber());

        if (!$instance->hasDeduction()) {
            $datiRitenuta = $documentGeneralData->addChild('DatiRitenuta');
            $datiRitenuta->addChild('TipoRitenuta', $instance->getDeductionType());
            $datiRitenuta->addChild('ImportoRitenuta', number_format($instance->getDeductionAmount(), '2', '.', ''));
            $datiRitenuta->addChild('AliquotaRitenuta', $instance->getDeductionPercentage());
            $datiRitenuta->addChild('CausalePagamento', SimpleXmlExtended::sanitizeText($instance->getDeductionDescription()));
        }

        if ($instance->getVirtualDuty()) {
            $datiBollo = $documentGeneralData->addChild('DatiBollo');
            $datiBollo->addChild('BolloVirtuale', 'SI');
            $datiBollo->addChild('ImportoBollo', $instance->getVirtualDutyAmount());
        }

        foreach ($instance->getFunds() as $documentCassa) {

            $datiCassa = $documentGeneralData->addChild('DatiCassaPrevidenziale');
            $datiCassa->addChild('TipoCassa', $documentCassa->getFundType());
            $datiCassa->addChild('AlCassa', $documentCassa->getFundPercentage());
            $datiCassa->addChild('ImportoContributoCassa', empty($documentCassa->getFundAmount()) ? '0.00' : $documentCassa->getImportoContributoCassa());

            $imponibileCassa = $documentCassa->getFundSubtotal();
            if ($imponibileCassa !== null) {
                $datiCassa->addChild('ImponibileCassa', $imponibileCassa);
            }

            $datiCassa->addChild('AliquotaIVA', $documentCassa->getFundTaxPercentage());

            if ($documentCassa->isFundDeduction()) {
                $datiCassa->addChild('Ritenuta', 'SI');
            }

            if ($documentCassa->getFundVatNature()) {
                $datiCassa->addChild('Natura', $documentCassa->getFundVatNature());
            }

            if (!empty($documentCassa->getFundRepresentative())) {
                $datiCassa->addChild('RiferimentoAmministrazione', SimpleXmlExtended::sanitizeText($documentCassa->getFundRepresentative()));
            }
        }

        foreach ($instance->getDiscounts() as $documentDiscount) {
            $datiScontoMaggiorazione = $documentGeneralData->addChild('ScontoMaggiorazione');
            $datiScontoMaggiorazione->addChild('Tipo', $documentDiscount->getType());

            $percentualeSconto = $documentDiscount->getPercentage();
            if ($percentualeSconto !== null) {
                $datiScontoMaggiorazione->addChild('Percentuale', $percentualeSconto);
            }

            $importoSconto = $documentDiscount->getAmount();
            if ($importoSconto !== null) {
                $datiScontoMaggiorazione->addChild('Importo', $importoSconto);
            }
        }

        $importoTotaleDocumento = $instance->getDocumentTotalTaxAmount();
        if ($importoTotaleDocumento !== null) {
            $documentGeneralData->addChild('ImportoTotaleDocumento', $importoTotaleDocumento);
        }

        if (!empty($instance->getDescriptions())) {
            foreach ($instance->getDescriptions() as $description) {
                $documentGeneralData->addChild('Causale', SimpleXmlExtended::sanitizeText($description));
            }
        }

        if ($instance->isArt73()) {
            $documentGeneralData->addChild('Art73', 'SI');
        }

        /* Dati ordini di acquisto */
        foreach ($instance->getPurchaseOrders() as $dataOrder) {
            $this->addExternalDocument($dataOrder, $generalData->addChild('DatiOrdineAcquisto'));
        }

        /* Dati convenzioni */
        foreach ($instance->getContracts() as $dataContract) {
            $this->addExternalDocument($dataContract, $generalData->addChild('DatiContratti'));
        }

        /* Dati contratti */
        foreach ($instance->getConventions() as $dataContract) {
            $this->addExternalDocument($dataContract, $generalData->addChild('DatiRicezione'));
        }

        /* Dati contratti */
        foreach ($instance->getReceipts() as $dataContract) {
            $this->addExternalDocument($dataContract, $generalData->addChild('DatiRicezione'));
        }

        /* Dati fatture */
        foreach ($instance->getRelatedInvoices() as $dataInvoice) {
            $this->addExternalDocument($dataInvoice, $generalData->addChild('DatiFattureCollegate'));
        }

        /* Dati SAL */
        foreach ($instance->getSals() as $sal) {
            $generalData->addChild('DatiSAL')->addChild('RiferimentoFase', (int)$sal);
        }

        /* Dati documenti DDT */
        /** @var \Weble\FatturaElettronica\ShippingLabel $documentDdt */
        foreach ($instance->getShippingLabels() as $documentDdt) {

            $ddtData = $generalData->addChild('DatiDDT');
            $ddtData->addChild('NumeroDDT', SimpleXmlExtended::sanitizeText($documentDdt->getDocumentNumber()));
            $ddtData->addChild('DataDDT', $documentDdt->getDocumentDate()->format(DATE_ISO8601));
            $riferimentoLinea = $documentDdt->getLineNumberReference();
            if ($riferimentoLinea !== null) {
                foreach (explode(',', $riferimentoLinea) as $riferimentoLineaPart) {
                    $ddtData->addChild('RiferimentoNumeroLinea', SimpleXmlExtended::sanitizeText($riferimentoLineaPart));
                }
            }
        }

        return $this;
    }

    protected function addExternalDocument (RelatedDocumentInterface $documentData, SimpleXMLElement $parent)
    {
        $riferimentoLinea = $documentData->getLineNumberReference();
        if ($riferimentoLinea !== null) {
            foreach (explode(',', $riferimentoLinea) as $riferimentoLineaPart) {
                $parent->addChild('RiferimentoNumeroLinea', SimpleXmlExtended::sanitizeText($riferimentoLineaPart));
            }
        }

        $parent->addChild('IdDocumento', SimpleXmlExtended::sanitizeText($documentData->getDocumentNumber()));

        if (!empty($documentData->getDocumentDate())) {
            $parent->addChild('Data', $documentData->getDocumentDate());
        }

        $numItem = $documentData->getLineNumber();
        if ($numItem !== null) {
            $parent->addChild('NumItem', SimpleXmlExtended::sanitizeText(getLineNumber));
        }

        $codiceCommessa = $documentData->getOrderCode();
        if ($codiceCommessa !== null) {
            $parent->addChild('CodiceCommessaConvenzione', SimpleXmlExtended::sanitizeText($codiceCommessa));
        }

        $codiceCup = $documentData->getCupCode();
        if ($codiceCup !== null) {
            $parent->addChild('CodiceCUP', SimpleXmlExtended::sanitizeText($codiceCup));
        }

        $codiceCig = $documentData->getCigCode();
        if (!$codiceCig !== null) {
            $parent->addChild('CodiceCIG', SimpleXmlExtended::sanitizeText($codiceCig));
        }
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
