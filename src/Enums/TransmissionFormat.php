<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum TransmissionFormat: string
{
	case FPA12 = 'FPA12';
	case FPR12 = 'FPR12';
	case FSM10 = 'FSM10';

    public function getLabel(): string
    {
        return match($this) {
			self::FPA12 => 'Fattura verso PA',
			self::FPR12 => 'Fattura verso privati',
			self::FSM10 => 'Fattura verso privati semplificata',

            default => null,
        };
    }
}
