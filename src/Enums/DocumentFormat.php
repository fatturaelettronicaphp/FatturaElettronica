<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

use TypeError;

class DocumentFormat
{
    public const XML = 'xml';
    public const P7M = 'p7m';

    public static function from(string $extension): string
    {
        switch (strtolower($extension)) {
            case self::XML:
                return self::XML;
            case self::P7M:
                return self::P7M;
        }

        throw new TypeError('Extension Format not supported: ' . $extension);
    }
}
