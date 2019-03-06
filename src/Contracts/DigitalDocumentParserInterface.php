<?php

namespace Weble\FatturaElettronica\Contracts;

use SimpleXMLElement;
use Weble\FatturaElettronica\DigitalDocumentInstance;

interface DigitalDocumentParserInterface
{
    public function parse (DigitalDocumentInterface $digitalDocument = null): DigitalDocumentInterface;

    public function xml (): SimpleXMLElement;

    public function extractCustomerInformations (SimpleXMLElement $simpleXml): BillableInterface;

    public function extractSupplierInformations (SimpleXMLElement $simpleXml): BillableInterface;
}