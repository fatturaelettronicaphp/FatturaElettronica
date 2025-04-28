<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum CancelType: string
{
	case SC = 'SC';
	case PR = 'PR';
	case AB = 'AB';
	case AC = 'AC';

    public function getLabel(): string
    {
        return match($this) {
			self::SC => 'Sconto',
			self::PR => 'Premio',
			self::AB => 'Abbuono',
			self::AC => 'Spesa accessoria',

            default => null,
        };
    }
}
