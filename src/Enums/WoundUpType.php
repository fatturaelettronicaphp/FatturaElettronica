<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum WoundUpType: string
{
	case LS = 'LS';
	case LN = 'LN';

    public function getLabel(): string
    {
        return match($this) {
			self::LS => 'in liquidazione',
			self::LN => 'non in liquidazione',

            default => null,
        };
    }
}
