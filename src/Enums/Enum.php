<?php

namespace Weble\FatturaElettronica\Enums;

abstract class Enum extends \Spatie\Enum\Enum
{
    protected static $descriptions = [];

    /** @var string */
    public $description;

    public function __construct (string $value = null)
    {
        parent::__construct($value);

        $this->description = $value;

        if (array_key_exists($this->value, static::$descriptions)) {
            $this->description = static::$descriptions[$this->value];
        }
    }
}