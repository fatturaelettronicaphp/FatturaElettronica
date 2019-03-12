<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DiscountInterface;
use Weble\FatturaElettronica\Contracts\OtherDataInterface;
use Weble\FatturaElettronica\Contracts\ProductInterface;
use Weble\FatturaElettronica\Enums\DocumentType;
use Weble\FatturaElettronica\Enums\DeductionType;
use DateTime;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;
use Weble\FatturaElettronica\Enums\VatNature;
use Weble\FatturaElettronica\Enums\FundType;
use Weble\FatturaElettronica\Enums\DiscountType;
use Weble\FatturaElettronica\Fund;

class OtherData implements ArrayableInterface, OtherDataInterface
{
    use Arrayable;

    /** @var string */
    protected $type;
    /** @var string */
    protected $text;
    /** @var float */
    protected $number;
    /** @var DateTime */
    protected $date;

    /**
     * @return string
     */
    public function getType (): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return OtherDataInterface
     */
    public function setType (?string $type): OtherDataInterface
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getText (): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return OtherDataInterface
     */
    public function setText (?string $text): OtherDataInterface
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return float
     */
    public function getNumber (): ?float
    {
        return $this->number;
    }

    /**
     * @param float $number
     *
     * @return OtherDataInterface
     */
    public function setNumber (?float $number): OtherDataInterface
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate (): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return OtherDataInterface
     */
    public function setDate ($date, $format = null): OtherDataInterface
    {
        if ($date === null) {
            return $this;
        }

        if ($format !== null) {
            $this->date = DateTime::createFromFormat($format, $date);
            return $this;
        }

        if ($date instanceof DateTime) {
            $this->date = $date;
            return $this;
        }

        $this->date = new DateTime($date);
        return $this;
    }


}
