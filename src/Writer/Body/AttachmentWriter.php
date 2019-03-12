<?php

namespace Weble\FatturaElettronica\Writer\Body;


use Weble\FatturaElettronica\Exceptions\InvalidDocument;

class AttachmentWriter extends AbstractBodyWriter
{
    protected function performWrite ()
    {
        if (!$this->body->hasAttachments()) {
            return $this->xml;
        }

        $xml = $this->xml->addChild('Allegati');

        /** @var \Weble\FatturaElettronica\Contracts\AttachmentInterface $attachment */
        foreach ($this->body->getAttachments() as $attachment) {
            if (!$attachment->getName()) {
                throw new InvalidDocument('<NomeAttachment> is required');
            }

            if (!$attachment->getFileData()) {
                throw new InvalidDocument('<Attachment> is required');
            }

            $xml->addChild('NomeAttachment', $attachment->getName());
            $xml->addChild('Attachment', base64_encode($attachment->getFileData()));

            if ($attachment->getCompression()) {
                $xml->addChild('AlgoritmoCompressione', $attachment->getCompression());
            }

            if ($attachment->getFormat()) {
                $xml->addChild('FormatoAttachment', $attachment->getFormat());
            }

            if ($attachment->getDescription()) {
                $xml->addChild('DescrizioneAttachment', $attachment->getDescription());
            }
        }
    }

}
