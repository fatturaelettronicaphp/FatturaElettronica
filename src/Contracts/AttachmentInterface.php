<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use FatturaElettronicaPhp\FatturaElettronica\Attachment;

interface AttachmentInterface
{
    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return Attachment
     */
    public function setName(?string $name);

    /**
     * @return string
     */
    public function getCompression(): ?string;

    /**
     * @param string $compression
     * @return Attachment
     */
    public function setCompression(?string $compression);

    /**
     * @return string
     */
    public function getFormat(): ?string;

    /**
     * @param string $format
     * @return Attachment
     */
    public function setFormat(?string $format);

    /**
     * @return string
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     * @return Attachment
     */
    public function setDescription(?string $description);

    /**
     * @return string
     */
    public function getAttachment(): ?string;

    /**
     * @param string $attachment
     * @return Attachment
     */
    public function setAttachment(?string $attachment);

    public function getFileData();

    public function writeFileToFolder($filePath = null): string;
}
