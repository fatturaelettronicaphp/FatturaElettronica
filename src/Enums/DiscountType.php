<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum DiscountType: string
{
	case SC = 'SC';
	case MG = 'MG';

    public function getLabel(): string
    {
        return match($this) {
			self::SC => 'SC = Sconto',
			self::MG => 'MG = Maggiorazione',

            default => null,
        };
    }
}
