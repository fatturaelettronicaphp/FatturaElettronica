<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Body;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\AttachmentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidDocument;

class AttachmentWriter extends AbstractBodyWriter
{
    protected function performWrite()
    {
        if (! $this->body->hasAttachments()) {
            return $this->xml;
        }

        /** @var AttachmentInterface $attachment */
        foreach ($this->body->getAttachments() as $attachment) {
            $xml = $this->xml->addChild('Allegati');

            if (! $attachment->getName()) {
                throw new InvalidDocument('<NomeAttachment> is required');
            }

            if (! $attachment->getName()) {
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

            $xml->addChild('Attachment', base64_encode((string) $attachment->getFileData()));
        }
    }
}
