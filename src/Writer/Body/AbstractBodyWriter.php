<?php

namespace Weble\FatturaElettronica\Writer\Body;

use SimpleXMLElement;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\RelatedDocumentInterface;
use Weble\FatturaElettronica\Utilities\SimpleXmlExtended;
use Weble\FatturaElettronica\Writer\AbstractWriter;

abstract class AbstractBodyWriter extends AbstractWriter
{
    /** @var DigitalDocumentInstanceInterface */
    protected $body;


    public function write ($body): SimpleXMLElement
    {
        $this->body = $body;

        $this->performWrite();

        return $this->xml;
    }

    abstract protected function performWrite();

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
            $parent->addChild('Data', $documentData->getDocumentDate()->format('YYYY-MM-DD'));
        }

        $numItem = $documentData->getLineNumber();
        if ($numItem !== null) {
            $parent->addChild('NumItem', SimpleXmlExtended::sanitizeText($numItem));
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
        if ($codiceCig !== null) {
            $parent->addChild('CodiceCIG', SimpleXmlExtended::sanitizeText($codiceCig));
        }
    }
}
