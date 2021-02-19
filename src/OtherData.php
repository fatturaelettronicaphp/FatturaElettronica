<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\OtherDataInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\Arrayable;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return OtherDataInterface
     */
    public function setType(?string $type): OtherDataInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return OtherDataInterface
     */
    public function setText(?string $text): OtherDataInterface
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return float
     */
    public function getNumber(): ?float
    {
        return $this->number;
    }

    /**
     * @param float $number
     *
     * @return OtherDataInterface
     */
    public function setNumber(?float $number): OtherDataInterface
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return OtherDataInterface
     */
    public function setDate($date, $format = null): OtherDataInterface
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
