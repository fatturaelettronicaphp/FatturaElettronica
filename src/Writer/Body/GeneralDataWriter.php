<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Body;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\AddressInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\BillableInterface;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DiscountInterface;
use FatturaElettronicaPhp\FatturaElettronica\Deduction;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidDocument;
use FatturaElettronicaPhp\FatturaElettronica\Fund;
use FatturaElettronicaPhp\FatturaElettronica\Shipment;
use FatturaElettronicaPhp\FatturaElettronica\Shipper;
use FatturaElettronicaPhp\FatturaElettronica\ShippingLabel;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\SimpleXmlExtended;
use SimpleXMLElement;

class GeneralDataWriter extends AbstractBodyWriter
{
    protected function performWrite()
    {
        $generalData = $this->xml->addChild('DatiGenerali');

        $this->addGeneralData($generalData);

        /* Dati ordini di acquisto */
        foreach ($this->body->getPurchaseOrders() as $dataOrder) {
            $this->addExternalDocument($dataOrder, $generalData->addChild('DatiOrdineAcquisto'));
        }

        /* Dati convenzioni */
        foreach ($this->body->getContracts() as $dataContract) {
            $this->addExternalDocument($dataContract, $generalData->addChild('DatiContratto'));
        }

        /* Dati contratti */
        foreach ($this->body->getConventions() as $dataConvention) {
            $this->addExternalDocument($dataConvention, $generalData->addChild('DatiConvenzione'));
        }

        /* Dati contratti */
        foreach ($this->body->getReceipts() as $dataReceipt) {
            $this->addExternalDocument($dataReceipt, $generalData->addChild('DatiRicezione'));
        }

        /* Dati fatture */
        foreach ($this->body->getRelatedInvoices() as $dataInvoice) {
            $this->addExternalDocument($dataInvoice, $generalData->addChild('DatiFattureCollegate'));
        }

        /* Dati SAL */
        foreach ($this->body->getSals() as $sal) {
            $generalData->addChild('DatiSAL')->addChild('RiferimentoFase', (int)$sal);
        }

        /* Dati documenti DDT */
        /** @var ShippingLabel $documentDdt */
        foreach ($this->body->getShippingLabels() as $documentDdt) {
            $this->addShippingLabelsData($generalData, $documentDdt);
        }

        $shipment = $this->body->getShipment();
        if ($shipment !== null) {
            $this->addShipment($generalData, $shipment);
        }

        $this->addMainInvoice($generalData);

        return $this;
    }

    protected function addGeneralData(SimpleXMLElement $generalData)
    {
        $documentGeneralData = $generalData->addChild('DatiGeneraliDocumento');
        $documentGeneralData->addChild('TipoDocumento', (string)$this->body->getDocumentType());
        $documentGeneralData->addChild('Divisa', $this->body->getCurrency());
        $documentGeneralData->addChild('Data', $this->body->getDocumentDate()->format('Y-m-d'));
        $documentGeneralData->addChild('Numero', $this->body->getDocumentNumber());

        if ($this->body->hasDeduction()) {
            $this->addDeductionData($documentGeneralData);
        }

        foreach ($this->body->getDeductions() as $deduction) {
            $this->addDeductionData($documentGeneralData, $deduction);
        }

        if ($this->body->getVirtualDuty()) {
            $this->addVirtualDutyData($documentGeneralData);
        }

        foreach ($this->body->getFunds() as $documentCassa) {
            $this->addFundData($documentGeneralData, $documentCassa);
        }

        foreach ($this->body->getDiscounts() as $documentDiscount) {
            $this->addDiscountData($documentGeneralData, $documentDiscount);
        }

        $importoTotaleDocumento = $this->body->getDocumentTotal();
        if ($importoTotaleDocumento !== null) {
            $documentGeneralData->addChild('ImportoTotaleDocumento', SimpleXmlExtended::sanitizeFloat($importoTotaleDocumento));
        }

        $value = $this->body->getRounding();
        if ($value !== null) {
            $documentGeneralData->addChild('Arrotondamento', SimpleXmlExtended::sanitizeFloat($value));
        }

        if (! empty($this->body->getDescriptions())) {
            foreach ($this->body->getDescriptions() as $description) {
                $documentGeneralData->addChild('Causale', SimpleXmlExtended::sanitizeText($description));
            }
        }

        if ($this->body->isArt73()) {
            $documentGeneralData->addChild('Art73', 'SI');
        }
    }

    /**
     * @param SimpleXMLElement $documentGeneralData
     */
    protected function addDeductionData(SimpleXMLElement $documentGeneralData, Deduction $deduction = null): void
    {
        if ($deduction instanceof Deduction) {
            $datiRitenuta = $documentGeneralData->addChild('DatiRitenuta');
            $datiRitenuta->addChild('TipoRitenuta', $deduction->getType());
            $datiRitenuta->addChild('ImportoRitenuta', SimpleXmlExtended::sanitizeFloat($deduction->getAmount()));
            $datiRitenuta->addChild('AliquotaRitenuta', SimpleXmlExtended::sanitizeFloat($deduction->getPercentage()));
            $datiRitenuta->addChild('CausalePagamento', SimpleXmlExtended::sanitizeText($deduction->getDescription()));
        } else {
            $datiRitenuta = $documentGeneralData->addChild('DatiRitenuta');
            $datiRitenuta->addChild('TipoRitenuta', $this->body->getDeductionType());
            $datiRitenuta->addChild('ImportoRitenuta', SimpleXmlExtended::sanitizeFloat($this->body->getDeductionAmount()));
            $datiRitenuta->addChild('AliquotaRitenuta', SimpleXmlExtended::sanitizeFloat($this->body->getDeductionPercentage()));
            $datiRitenuta->addChild('CausalePagamento', SimpleXmlExtended::sanitizeText($this->body->getDeductionDescription()));
        }
    }

    /**
     * @param SimpleXMLElement $documentGeneralData
     */
    protected function addVirtualDutyData(SimpleXMLElement $documentGeneralData): void
    {
        $datiBollo = $documentGeneralData->addChild('DatiBollo');
        $datiBollo->addChild('BolloVirtuale', 'SI');
        $datiBollo->addChild('ImportoBollo', SimpleXmlExtended::sanitizeFloat($this->body->getVirtualDutyAmount()));
    }

    /**
     * @param SimpleXMLElement $documentGeneralData
     * @param Fund $documentCassa
     */
    protected function addFundData(SimpleXMLElement $documentGeneralData, Fund $documentCassa): void
    {
        $datiCassa = $documentGeneralData->addChild('DatiCassaPrevidenziale');
        $datiCassa->addChild('TipoCassa', $documentCassa->getType());
        $datiCassa->addChild('AlCassa', SimpleXmlExtended::sanitizeFloat($documentCassa->getPercentage()));
        $datiCassa->addChild('ImportoContributoCassa', SimpleXmlExtended::sanitizeFloat($documentCassa->getAmount()));

        $imponibileCassa = $documentCassa->getSubtotal();
        if ($imponibileCassa !== null) {
            $datiCassa->addChild('ImponibileCassa', SimpleXmlExtended::sanitizeFloat($imponibileCassa));
        }

        $datiCassa->addChild('AliquotaIVA', SimpleXmlExtended::sanitizeFloat($documentCassa->getTaxPercentage()));

        if ($documentCassa->hasDeduction()) {
            $datiCassa->addChild('Ritenuta', 'SI');
        }

        if ($documentCassa->getVatNature()) {
            $datiCassa->addChild('Natura', $documentCassa->getVatNature());
        }

        if (! empty($documentCassa->getRepresentative())) {
            $datiCassa->addChild('RiferimentoAmministrazione', SimpleXmlExtended::sanitizeText($documentCassa->getRepresentative()));
        }
    }

    /**
     * @param SimpleXMLElement $documentGeneralData
     * @param DiscountInterface $documentDiscount
     */
    protected function addDiscountData(SimpleXMLElement $documentGeneralData, DiscountInterface $documentDiscount): void
    {
        $datiScontoMaggiorazione = $documentGeneralData->addChild('ScontoMaggiorazione');
        $datiScontoMaggiorazione->addChild('Tipo', $documentDiscount->getType());

        $percentualeSconto = $documentDiscount->getPercentage();
        if ($percentualeSconto !== null) {
            $datiScontoMaggiorazione->addChild('Percentuale', SimpleXmlExtended::sanitizeFloat($percentualeSconto));
        }

        $importoSconto = $documentDiscount->getAmount();
        if ($importoSconto !== null) {
            $datiScontoMaggiorazione->addChild('Importo', SimpleXmlExtended::sanitizeFloat($importoSconto));
        }
    }

    /**
     * @param SimpleXMLElement $generalData
     * @param ShippingLabel $documentDdt
     */
    protected function addShippingLabelsData(SimpleXMLElement $generalData, ShippingLabel $documentDdt): void
    {
        $ddtData = $generalData->addChild('DatiDDT');
        $ddtData->addChild('NumeroDDT', SimpleXmlExtended::sanitizeText($documentDdt->getDocumentNumber()));

        if ($documentDdt->getDocumentDate()) {
            $ddtData->addChild('DataDDT', $documentDdt->getDocumentDate()->format('Y-m-d'));
        }

        $riferimentiLinea = $documentDdt->getLineNumberReferences();
        foreach ($riferimentiLinea as $riferimentoLinea) {
            if ($riferimentoLinea === null) {
                continue;
            }

            $ddtData->addChild('RiferimentoNumeroLinea', SimpleXmlExtended::sanitizeText($riferimentoLinea));
        }
    }

    /**
     * @param SimpleXMLElement $shipmentData
     * @param BillableInterface|null $documentPerson
     */
    protected function addShipperInfo(SimpleXMLElement $shipmentData, ?BillableInterface $documentPerson): void
    {
        $datiAnagrafici = $shipmentData->addChild('DatiAnagraficiVettore');

        $idPaese = $documentPerson->getCountryCode();
        $vatNumber = $documentPerson->getVatNumber();

        $fiscalData = $this->calculateFiscalData($idPaese, null, $vatNumber);

        $idFiscaleIva = $datiAnagrafici->addChild('IdFiscaleIVA');
        $idFiscaleIva->addChild('IdPaese', $idPaese);
        $idFiscaleIva->addChild('IdCodice', $fiscalData['idCodice']);

        if (! empty($fiscalData['codiceFiscale'])) {
            $datiAnagrafici->addChild('CodiceFiscale', SimpleXmlExtended::sanitizeText($fiscalData['codiceFiscale']));
        }

        $anagrafica = $datiAnagrafici->addChild('Anagrafica');

        if ($documentPerson instanceof Shipper && ! empty($documentPerson->getLicenseNumber())) {
            $datiAnagrafici->addChild('NumeroLicenzaGuida', substr(SimpleXmlExtended::sanitizeText($documentPerson->getLicenseNumber()), 0, 20));
        }

        if (! empty($documentPerson->getOrganization())) {
            $anagrafica->addChild('Denominazione', SimpleXmlExtended::sanitizeText($documentPerson->getOrganization()));

            return;
        }

        if (! empty($documentPerson->getName())) {
            $anagrafica->addChild('Nome', SimpleXmlExtended::sanitizeText($documentPerson->getName()));
        }

        if (! empty($documentPerson->getSurname())) {
            $anagrafica->addChild('Cognome', SimpleXmlExtended::sanitizeText($documentPerson->getSurname()));
        }
    }

    /**
     * @param SimpleXMLElement $shipmentData
     * @param AddressInterface|null $address
     */
    protected function addShipmentAddress(SimpleXMLElement $shipmentData, ?AddressInterface $address): void
    {
        $addressData = $shipmentData->addChild('IndirizzoResa');
        if ($address->getStreet() === null) {
            throw new InvalidDocument('<Indirizzo> in <IndirizzoResa> è obbligatorio se presente <IndirizzoResa>');
        }

        if ($address->getZip() === null) {
            throw new InvalidDocument('<CAP> in <IndirizzoResa> è obbligatorio se presente <IndirizzoResa>');
        }

        if ($address->getCity() === null) {
            throw new InvalidDocument('<Comune> in <IndirizzoResa> è obbligatorio se presente <IndirizzoResa>');
        }

        if ($address->getCountryCode() === null) {
            throw new InvalidDocument('<Nazione> in <IndirizzoResa> è obbligatorio se presente <IndirizzoResa>');
        }

        $addressData->addChild('Indirizzo', $address->getStreet());

        $value = $address->getStreetNumber();
        if ($value !== null) {
            $addressData->addChild('NumeroCivico', $value);
        }

        $addressData->addChild('CAP', $address->getZip());
        $addressData->addChild('Comune', $address->getCity());

        $value = $address->getState();
        if ($value !== null) {
            $addressData->addChild('Provincia', $value);
        }

        $addressData->addChild('Nazione', $address->getCountryCode());
    }

    /**
     * @param SimpleXMLElement $generalData
     * @param Shipment|null $shipment
     */
    protected function addShipment(SimpleXMLElement $generalData, ?Shipment $shipment): void
    {
        $shipmentData = $generalData->addChild('DatiTrasporto');

        /** @var Shipper $documentPerson */
        $documentPerson = $shipment->getShipper();

        if ($documentPerson !== null) {
            $this->addShipperInfo($shipmentData, $documentPerson);
        }

        $value = $shipment->getMethod();
        if ($value !== null) {
            $shipmentData->addChild('MezzoTrasporto', $value);
        }

        $value = $shipment->getShipmentDescription();
        if ($value !== null) {
            $shipmentData->addChild('CausaleTrasporto', $value);
        }

        $value = $shipment->getNumberOfPackages();
        if ($value !== null) {
            $shipmentData->addChild('NumeroColli', $value);
        }

        $value = $shipment->getDescription();
        if ($value !== null) {
            $shipmentData->addChild('Descrizione', $value);
        }

        $value = $shipment->getWeightUnit();
        if ($value !== null) {
            $shipmentData->addChild('UnitaMisuraPeso', $value);
        }

        $value = $shipment->getWeight();
        if ($value !== null) {
            $shipmentData->addChild('PesoLordo', SimpleXmlExtended::sanitizeFloat($value));
        }

        $value = $shipment->getNetWeight();
        if ($value !== null) {
            $shipmentData->addChild('PesoNetto', SimpleXmlExtended::sanitizeFloat($value));
        }

        $value = $shipment->getPickupDate();
        if ($value !== null) {
            $shipmentData->addChild('DataOraRitiro', $value->format('Y-m-d\TH:i:s.000P'));
        }

        $value = $shipment->getShipmentDate();
        if ($value !== null) {
            $shipmentData->addChild('DataInizioTrasporto', $value->format('Y-m-d'));
        }

        $value = $shipment->getReturnType();
        if ($value !== null) {
            $shipmentData->addChild('TipoResa', $value);
        }

        $address = $shipment->getReturnAddress();
        if ($address !== null) {
            $this->addShipmentAddress($shipmentData, $address);
        }

        $value = $shipment->getDeliveryDate();
        if ($value !== null) {
            $shipmentData->addChild('DataOraConsegna', $value->format('Y-m-d\TH:i:s.000P'));
        }
    }

    /**
     * @param SimpleXMLElement $generalData
     */
    protected function addMainInvoice(SimpleXMLElement $generalData): void
    {
        if ($this->body->getMainInvoiceDate() !== null || $this->body->getMainInvoiceNumber() !== null) {
            $mainInvoiceData = $generalData->addChild('FatturaPrincipale');

            $value = $this->body->getMainInvoiceNumber();
            if ($value !== null) {
                $mainInvoiceData->addChild('NumeroFatturaPrincipale', $value);
            }

            $value = $this->body->getMainInvoiceDate();
            if ($value !== null) {
                $mainInvoiceData->addChild('DataFatturaPrincipale', $value->format('Y-m-d'));
            }
        }
    }
}
