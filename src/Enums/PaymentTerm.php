<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum PaymentTerm: string
{
	case TP01 = 'TP01';
	case TP02 = 'TP02';
	case TP03 = 'TP03';

    public function getLabel(): string
    {
        return match($this) {
			self::TP01 => 'pagamento a rate',
			self::TP02 => 'pagamento completo',
			self::TP03 => 'anticipo',

            default => null,
        };
    }
}
