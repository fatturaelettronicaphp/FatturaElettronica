<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum DeductionType: string
{
	case RT01 = 'RT01';
	case RT02 = 'RT02';
	case RT03 = 'RT03';
	case RT04 = 'RT04';
	case RT05 = 'RT05';
	case RT06 = 'RT06';

    public function getLabel(): string
    {
        return match($this) {
			self::RT01 => 'Ritenuta di acconto persone fisiche',
			self::RT02 => 'Ritenuta di acconto persone giuridiche',
			self::RT03 => 'Contributo INPS',
			self::RT04 => 'Contributo ENASARCO',
			self::RT05 => 'Contributo ENPAM',
			self::RT06 => 'Altro contributo previdenziale',

            default => null,
        };
    }
}
