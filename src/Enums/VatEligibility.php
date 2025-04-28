<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum VatEligibility: string
{
	case D = 'D';
	case I = 'I';
	case S = 'S';

    public function getLabel(): string
    {
        return match($this) {
			self::D => 'esigibilità differita',
			self::I => 'esigibilità immediata',
			self::S => 'scissione dei pagamenti',

            default => null,
        };
    }
}
