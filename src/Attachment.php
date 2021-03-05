<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\AttachmentInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Attachment implements ArrayableInterface, AttachmentInterface
{
    use Arrayable;

    /** @var string */
    protected $name;
    /** @var string */
    protected $compression;
    /** @var string */
    protected $format;
    /** @var string */
    protected $description;
    /** @var string */
    protected $attachment;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Attachment
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompression(): ?string
    {
        return $this->compression;
    }

    /**
     * @param string $compression
     * @return Attachment
     */
    public function setCompression(?string $compression): self
    {
        $this->compression = $compression;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return Attachment
     */
    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Attachment
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    /**
     * @param string $attachment
     * @return Attachment
     */
    public function setAttachment(?string $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }

    public function getFileData(): string
    {
        return base64_decode($this->getAttachment());
    }

    public function writeFileToFolder($filePath = null): string
    {
        if ($filePath === null) {
            $folder   = tempnam(sys_get_temp_dir(), 'fattura_elettronica');
            $filePath =  $folder . $this->getName();
        }

        $handle = fopen($filePath, 'w');
        fwrite($handle, $this->getFileData());
        fclose($handle);

        return $filePath;
    }
}
