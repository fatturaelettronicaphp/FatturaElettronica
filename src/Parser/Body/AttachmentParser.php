<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use FatturaElettronicaPhp\FatturaElettronica\Attachment;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\AttachmentInterface;

class AttachmentParser extends AbstractBodyParser
{
    protected function performParsing()
    {
        $attachments = (array)$this->extractValueFromXml('Allegati', false);
        foreach ($attachments as $attachment) {
            $instance = $this->extractAttachmentFrom($attachment);
            $this->digitalDocumentInstance->addAttachment($instance);
        }
    }

    /**
     * @param $attachment
     * @return Attachment
     */
    protected function extractAttachmentFrom($attachment): AttachmentInterface
    {
        $instance = new Attachment();

        $value = $this->extractValueFromXmlElement($attachment, 'NomeAttachment');
        $instance->setName($value);

        $value = $this->extractValueFromXmlElement($attachment, 'AlgoritmoCompressione');
        $instance->setCompression($value);

        $value = $this->extractValueFromXmlElement($attachment, 'FormatoAttachment');
        $instance->setFormat($value);

        $value = $this->extractValueFromXmlElement($attachment, 'DescrizioneAttachment');
        $instance->setDescription($value);

        $value = (string) $this->extractValueFromXmlElement($attachment, 'Attachment');
        $instance->setAttachment($value);

        return $instance;
    }
}
