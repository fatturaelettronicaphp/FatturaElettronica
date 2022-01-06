<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

use JsonSerializable;
use Stringable;
use TypeError;

abstract class EnvironmentEnum implements JsonSerializable
{
    protected static $values = [];

	const ENVIRONMENT_DEVELOPMENT = 'development';
	const ENVIRONMENT_PRODUCTION = 'production';
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

        if (!in_array($value,self::values())) {
            throw new TypeError("Value '{$value}' is not support on type " . static::class);
        }

        $this->value       = $value;
        $this->description = self::values()[$value] ?? $value;
    }

    public static function values(): array
    {
        return [self::ENVIRONMENT_DEVELOPMENT,self::ENVIRONMENT_PRODUCTION];
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
