<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

use JsonSerializable;
use Stringable;
use TypeError;

abstract class Enum implements Stringable, JsonSerializable
{
    protected static $values = [];

    protected const TYPE = '';

    /**
     * @var string
     */
    public $value;

    /**
     * @var string
     */
    public $description;

    public function __construct(string $value)
    {
        if (!isset(self::values()[$value])) {
            throw new TypeError("Value '{$value}' is not support on type " . static::class);
        }

        $this->value = $value;
        $this->description = self::values()[$value] ?? $value;
    }

    public static function values(): array
    {
        return XSDTypes::types(static::TYPE);
    }

    public static function __callStatic($name, $arguments): self
    {
        return new static(str_replace("_", ".", $name));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [$this->value => $this->description];
    }

    public function equals(Enum $enum): bool
    {
        return $enum->value === $this->value;
    }
}
