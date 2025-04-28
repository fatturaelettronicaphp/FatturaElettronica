<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum PaymentMethod: string
{
	case MP01 = 'MP01';
	case MP02 = 'MP02';
	case MP03 = 'MP03';
	case MP04 = 'MP04';
	case MP05 = 'MP05';
	case MP06 = 'MP06';
	case MP07 = 'MP07';
	case MP08 = 'MP08';
	case MP09 = 'MP09';
	case MP10 = 'MP10';
	case MP11 = 'MP11';
	case MP12 = 'MP12';
	case MP13 = 'MP13';
	case MP14 = 'MP14';
	case MP15 = 'MP15';
	case MP16 = 'MP16';
	case MP17 = 'MP17';
	case MP18 = 'MP18';
	case MP19 = 'MP19';
	case MP20 = 'MP20';
	case MP21 = 'MP21';
	case MP22 = 'MP22';
	case MP23 = 'MP23';

    public function getLabel(): string
    {
        return match($this) {
			self::MP01 => 'contanti',
			self::MP02 => 'assegno',
			self::MP03 => 'assegno circolare',
			self::MP04 => 'contanti presso Tesoreria',
			self::MP05 => 'bonifico',
			self::MP06 => 'vaglia cambiario',
			self::MP07 => 'bollettino bancario',
			self::MP08 => 'carta di pagamento',
			self::MP09 => 'RID',
			self::MP10 => 'RID utenze',
			self::MP11 => 'RID veloce',
			self::MP12 => 'RIBA',
			self::MP13 => 'MAV',
			self::MP14 => 'quietanza erario',
			self::MP15 => 'giroconto su conti di contabilità speciale',
			self::MP16 => 'domiciliazione bancaria',
			self::MP17 => 'domiciliazione postale',
			self::MP18 => 'bollettino di c/c postale',
			self::MP19 => 'SEPA Direct Debit',
			self::MP20 => 'SEPA Direct Debit CORE',
			self::MP21 => 'SEPA Direct Debit B2B',
			self::MP22 => 'Trattenuta su somme già riscosse',
			self::MP23 => 'PagoPA',

            default => null,
        };
    }
}
