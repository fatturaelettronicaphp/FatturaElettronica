<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Body;


use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidDocument;

class AttachmentWriter extends AbstractBodyWriter
{
    protected function performWrite ()
    {
        if (!$this->body->hasAttachments()) {
            return $this->xml;
        }

        $xml = $this->xml->addChild('Allegati');

        /** @var \FatturaElettronicaPhp\FatturaElettronica\Contracts\AttachmentInterface $attachment */
        foreach ($this->body->getAttachments() as $attachment) {
            if (!$attachment->getName()) {
                throw new InvalidDocument('<NomeAttachment> is required');
            }

            if (!$attachment->getFileData()) {
                throw new InvalidDocument('<Attachment> is required');
            }

            $xml->addChild('NomeAttachment', $attachment->getName());

            if ($attachment->getCompression()) {
                $xml->addChild('AlgoritmoCompressione', $attachment->getCompression());
            }

            if ($attachment->getFormat()) {
                $xml->addChild('FormatoAttachment', $attachment->getFormat());
            }

            if ($attachment->getDescription()) {
                $xml->addChild('DescrizioneAttachment', $attachment->getDescription());
            }

            $xml->addChild('Attachment', base64_encode($attachment->getFileData()));
        }
    }

}
