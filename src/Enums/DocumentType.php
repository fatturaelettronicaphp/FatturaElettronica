<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum DocumentType: string
{
	case TD01 = 'TD01';
	case TD02 = 'TD02';
	case TD03 = 'TD03';
	case TD04 = 'TD04';
	case TD05 = 'TD05';
	case TD06 = 'TD06';
	case TD16 = 'TD16';
	case TD17 = 'TD17';
	case TD18 = 'TD18';
	case TD19 = 'TD19';
	case TD20 = 'TD20';
	case TD21 = 'TD21';
	case TD22 = 'TD22';
	case TD23 = 'TD23';
	case TD24 = 'TD24';
	case TD25 = 'TD25';
	case TD26 = 'TD26';
	case TD27 = 'TD27';
	case TD28 = 'TD28';
	case TD29 = 'TD29';
	case TD07 = 'TD07';
	case TD08 = 'TD08';
	case TD09 = 'TD09';

    public function getLabel(): string
    {
        return match($this) {
			self::TD01 => 'Fattura',
			self::TD02 => 'Acconto / anticipo su fattura',
			self::TD03 => 'Acconto / anticipo su parcella',
			self::TD04 => 'Nota di credito',
			self::TD05 => 'Nota di debito',
			self::TD06 => 'Parcella',
			self::TD16 => 'Integrazione fattura reverse charge interno',
			self::TD17 => 'Integrazione/autofattura per acquisto servizi dall\'estero',
			self::TD18 => 'Integrazione per acquisto di beni intracomunitari',
			self::TD19 => 'Integrazione/autofattura per acquisto di beni ex art.17 c.2 DPR 633/72',
			self::TD20 => 'Autofattura per regolarizzazione e integrazione delle fatture (ex art. 6 c.9-bis d.lgs. 471/97 o art.46 c.5 D.L. 331/93)',
			self::TD21 => 'Autofattura per splafonamento',
			self::TD22 => 'Estrazione benida Deposito IVA',
			self::TD23 => 'Estrazione beni da Deposito IVA con versamento dell\'IVA',
			self::TD24 => 'Fattura differita di cui all\'art.21, comma 4, terzo periodo lett. a) DPR 633/72',
			self::TD25 => 'Fattura differita di cui all\'art.21, comma 4, terzo periodo lett. b) DPR 633/72',
			self::TD26 => 'Cessione di beni ammortizzabili e per passaggi interni (ex art.36 DPR 633/72)',
			self::TD27 => 'Fattura per autoconsumo o per cessioni gratuite senza rivalsa',
			self::TD28 => 'Acquisti da San Marino con IVA (fattura cartacea)',
			self::TD29 => 'Comunicazione per omessa o irregolare fatturazione da parte del cedente/prestatore italiano (art. 6, comma 8, D.Lgs. 471/97)',
			self::TD07 => 'Fattura semplificata',
			self::TD08 => 'Nota di credito semplificata',
			self::TD09 => 'Nota di debito semplificata',

            default => null,
        };
    }
}
