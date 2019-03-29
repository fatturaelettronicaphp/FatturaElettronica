<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use DateTime;

interface OtherDataInterface
{
    /**
     * @return string
     */
    public function getType (): ?string;

    /**
     * @param string $type
     *
     * @return OtherDataInterface
     */
    public function setType (?string $type);

    /**
     * @return string
     */
    public function getText (): ?string;

    /**
     * @param string $text
     *
     * @return OtherDataInterface
     */
    public function setText (?string $text);

    /**
     * @return float
     */
    public function getNumber (): ?float;

    /**
     * @param float $number
     *
     * @return OtherDataInterface
     */
    public function setNumber (?float $number);

    /**
     * @return DateTime
     */
    public function getDate (): ?DateTime;

    /**
     * @param DateTime $date
     *
     * @return OtherDataInterface
     */
    public function setDate ($date, $format = null);
}