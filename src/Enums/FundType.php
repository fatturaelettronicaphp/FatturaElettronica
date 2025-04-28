<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum FundType: string
{
	case TC01 = 'TC01';
	case TC02 = 'TC02';
	case TC03 = 'TC03';
	case TC04 = 'TC04';
	case TC05 = 'TC05';
	case TC06 = 'TC06';
	case TC07 = 'TC07';
	case TC08 = 'TC08';
	case TC09 = 'TC09';
	case TC10 = 'TC10';
	case TC11 = 'TC11';
	case TC12 = 'TC12';
	case TC13 = 'TC13';
	case TC14 = 'TC14';
	case TC15 = 'TC15';
	case TC16 = 'TC16';
	case TC17 = 'TC17';
	case TC18 = 'TC18';
	case TC19 = 'TC19';
	case TC20 = 'TC20';
	case TC21 = 'TC21';
	case TC22 = 'TC22';

    public function getLabel(): string
    {
        return match($this) {
			self::TC01 => 'Cassa nazionale previdenza e assistenza avvocati e procuratori legali',
			self::TC02 => 'Cassa previdenza dottori commercialisti',
			self::TC03 => 'Cassa previdenza e assistenza geometri',
			self::TC04 => 'Cassa nazionale previdenza e assistenza ingegneri e architetti liberi professionisti',
			self::TC05 => 'Cassa nazionale del notariato',
			self::TC06 => 'Cassa nazionale previdenza e assistenza ragionieri e periti commerciali',
			self::TC07 => 'Ente nazionale assistenza agenti e rappresentanti di commercio (ENASARCO)',
			self::TC08 => 'Ente nazionale previdenza e assistenza consulenti del lavoro (ENPACL)',
			self::TC09 => 'Ente nazionale previdenza e assistenza medici (ENPAM)',
			self::TC10 => 'Ente nazionale previdenza e assistenza farmacisti (ENPAF)',
			self::TC11 => 'Ente nazionale previdenza e assistenza veterinari (ENPAV)',
			self::TC12 => 'Ente nazionale previdenza e assistenza impiegati dell\'agricoltura (ENPAIA)',
			self::TC13 => 'Fondo previdenza impiegati imprese di spedizione e agenzie marittime',
			self::TC14 => 'Istituto nazionale previdenza giornalisti italiani (INPGI)',
			self::TC15 => 'Opera nazionale assistenza orfani sanitari italiani (ONAOSI)',
			self::TC16 => 'Cassa autonoma assistenza integrativa giornalisti italiani (CASAGIT)',
			self::TC17 => 'Ente previdenza periti industriali e periti industriali laureati (EPPI)',
			self::TC18 => 'Ente previdenza e assistenza pluricategoriale (EPAP)',
			self::TC19 => 'Ente nazionale previdenza e assistenza biologi (ENPAB)',
			self::TC20 => 'Ente nazionale previdenza e assistenza professione infermieristica (ENPAPI)',
			self::TC21 => 'Ente nazionale previdenza e assistenza psicologi (ENPAP)',
			self::TC22 => 'INPS',

            default => null,
        };
    }
}
