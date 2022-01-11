<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Header;

class AttributesWriter extends AbstractHeaderWriter
{
    protected function performWrite()
    {
        if ($this->document->getVersion()) {
            $this->xml->addAttribute('versione', $this->document->getVersion());
        }

        if ($this->document->getEmittingSystem()) {
            $this->xml->addAttribute('SistemaEmittente', substr($this->document->getEmittingSystem(), 0, 10));
        }

        return $this;
    }
}
