<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum EmittingSubject: string
{
	case CC = 'CC';
	case TZ = 'TZ';

    public function getLabel(): string
    {
        return match($this) {
			self::CC => 'Cessionario / Committente',
			self::TZ => 'Terzo',

            default => null,
        };
    }
}
