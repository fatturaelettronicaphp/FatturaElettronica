<?php

namespace Weble\FatturaElettronica\Writer\Header;

use Weble\FatturaElettronica\Enums\RecipientCode;
use Weble\FatturaElettronica\Utilities\SimpleXmlExtended;

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
