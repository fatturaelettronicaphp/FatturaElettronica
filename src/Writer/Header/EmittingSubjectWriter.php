<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Header;

class EmittingSubjectWriter extends AbstractHeaderWriter
{
    protected function performWrite ()
    {
        if (!empty($this->document->getEmittingSubject())) {
            $this->xml->addChild('SoggettoEmittente', $this->document->getEmittingSubject());
        }

        return $this;
    }

}
