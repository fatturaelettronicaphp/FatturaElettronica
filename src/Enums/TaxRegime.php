<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum TaxRegime: string
{
	case RF01 = 'RF01';
	case RF02 = 'RF02';
	case RF04 = 'RF04';
	case RF05 = 'RF05';
	case RF06 = 'RF06';
	case RF07 = 'RF07';
	case RF08 = 'RF08';
	case RF09 = 'RF09';
	case RF10 = 'RF10';
	case RF11 = 'RF11';
	case RF12 = 'RF12';
	case RF13 = 'RF13';
	case RF14 = 'RF14';
	case RF15 = 'RF15';
	case RF16 = 'RF16';
	case RF17 = 'RF17';
	case RF18 = 'RF18';
	case RF19 = 'RF19';
	case RF20 = 'RF20';

    public function getLabel(): string
    {
        return match($this) {
			self::RF01 => ' Regime ordinario',
			self::RF02 => 'Regime dei contribuenti minimi (art. 1,c.96-117, L. 244/2007)',
			self::RF04 => 'Agricoltura e attività connesse e pesca (artt. 34 e 34-bis, D.P.R. 633/1972)',
			self::RF05 => 'Vendita sali e tabacchi (art. 74, c.1, D.P.R. 633/1972)',
			self::RF06 => 'Commercio dei fiammiferi (art. 74, c.1, D.P.R. 633/1972)',
			self::RF07 => 'Editoria (art. 74, c.1, D.P.R. 633/1972)',
			self::RF08 => 'Gestione di servizi di telefonia pubblica (art. 74, c.1, D.P.R. 633/1972)',
			self::RF09 => 'Rivendita di documenti di trasporto pubblico e di sosta (art. 74, c.1, D.P.R. 633/1972)',
			self::RF10 => 'Intrattenimenti, giochi e altre attività	di cui alla tariffa allegata al D.P.R. 640/72 (art. 74, c.6, D.P.R. 633/1972)',
			self::RF11 => 'Agenzie di viaggi e turismo (art. 74-ter, D.P.R. 633/1972)',
			self::RF12 => 'Agriturismo (art. 5, c.2, L. 413/1991)',
			self::RF13 => 'Vendite a domicilio (art. 25-bis, c.6, D.P.R. 600/1973)',
			self::RF14 => 'Rivendita di beni usati, di oggetti	d’arte, d’antiquariato o da collezione (art.	36, D.L. 41/1995)',
			self::RF15 => 'Agenzie di vendite all’asta di oggetti d’arte, antiquariato o da collezione (art. 40-bis, D.L. 41/1995)',
			self::RF16 => 'IVA per cassa P.A. (art. 6, c.5, D.P.R. 633/1972)',
			self::RF17 => 'IVA per cassa (art. 32-bis, D.L. 83/2012)',
			self::RF18 => 'Altro',
			self::RF19 => 'Regime forfettario',
			self::RF20 => 'Regime transfrontaliero di Franchigia IVA (Direttiva UE 2020/285)',

            default => null,
        };
    }
}
