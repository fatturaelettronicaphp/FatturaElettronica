<?php

namespace Weble\FatturaElettronica\Writer\Body;

use Weble\FatturaElettronica\Enums\RecipientCode;
use Weble\FatturaElettronica\Utilities\SimpleXmlExtended;

class GeneralDataWriter extends AbstractBodyWriter
{
    protected function performWrite ()
    {
        $generalData = $this->xml->addChild('DatiGenerali');

        $documentGeneralData = $generalData->addChild('DatiGeneraliDocumento');
        $documentGeneralData->addChild('TipoDocumento', (string)$this->body->getDocumentType());
        $documentGeneralData->addChild('Divisa', $this->body->getCurrency());
        $documentGeneralData->addChild('Data', $this->body->getDocumentDate()->format('YYYY-MM-DD'));
        $documentGeneralData->addChild('Numero', $this->body->getDocumentNumber());

        if ($this->body->hasDeduction()) {
            $datiRitenuta = $documentGeneralData->addChild('DatiRitenuta');
            $datiRitenuta->addChild('TipoRitenuta', $this->body->getDeductionType());
            $datiRitenuta->addChild('ImportoRitenuta', number_format($this->body->getDeductionAmount(), '2', '.', ''));
            $datiRitenuta->addChild('AliquotaRitenuta', $this->body->getDeductionPercentage());
            $datiRitenuta->addChild('CausalePagamento', SimpleXmlExtended::sanitizeText($this->body->getDeductionDescription()));
        }

        if ($this->body->getVirtualDuty()) {
            $datiBollo = $documentGeneralData->addChild('DatiBollo');
            $datiBollo->addChild('BolloVirtuale', 'SI');
            $datiBollo->addChild('ImportoBollo', $this->body->getVirtualDutyAmount());
        }

        foreach ($this->body->getFunds() as $documentCassa) {

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

        foreach ($this->body->getDiscounts() as $documentDiscount) {
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

        $importoTotaleDocumento = $this->body->getDocumentTotalTaxAmount();
        if ($importoTotaleDocumento !== null) {
            $documentGeneralData->addChild('ImportoTotaleDocumento', $importoTotaleDocumento);
        }

        if (!empty($this->body->getDescriptions())) {
            foreach ($this->body->getDescriptions() as $description) {
                $documentGeneralData->addChild('Causale', SimpleXmlExtended::sanitizeText($description));
            }
        }

        if ($this->body->isArt73()) {
            $documentGeneralData->addChild('Art73', 'SI');
        }

        /* Dati ordini di acquisto */
        foreach ($this->body->getPurchaseOrders() as $dataOrder) {
            $this->addExternalDocument($dataOrder, $generalData->addChild('DatiOrdineAcquisto'));
        }

        /* Dati convenzioni */
        foreach ($this->body->getContracts() as $dataContract) {
            $this->addExternalDocument($dataContract, $generalData->addChild('DatiContratti'));
        }

        /* Dati contratti */
        foreach ($this->body->getConventions() as $dataContract) {
            $this->addExternalDocument($dataContract, $generalData->addChild('DatiConvenzione'));
        }

        /* Dati contratti */
        foreach ($this->body->getReceipts() as $dataContract) {
            $this->addExternalDocument($dataContract, $generalData->addChild('DatiRicezione'));
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
        /** @var \Weble\FatturaElettronica\ShippingLabel $documentDdt */
        foreach ($this->body->getShippingLabels() as $documentDdt) {

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

}
