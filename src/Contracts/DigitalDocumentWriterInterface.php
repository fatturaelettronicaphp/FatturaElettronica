<?php

namespace Weble\FatturaElettronica\Contracts;

use SimpleXMLElement;
use Weble\FatturaElettronica\DigitalDocumentInstance;

interface DigitalDocumentWriterInterface
{
    public function xml (): SimpleXMLElement;

    public function generate(): DigitalDocumentWriterInterface;

    public function write($filePath): bool ;
}