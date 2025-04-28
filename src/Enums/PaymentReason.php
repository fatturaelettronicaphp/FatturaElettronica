<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Enums;

enum PaymentReason: string
{
	case A = 'A';
	case B = 'B';
	case C = 'C';
	case D = 'D';
	case E = 'E';
	case G = 'G';
	case H = 'H';
	case I = 'I';
	case L = 'L';
	case M = 'M';
	case N = 'N';
	case O = 'O';
	case P = 'P';
	case Q = 'Q';
	case R = 'R';
	case S = 'S';
	case T = 'T';
	case U = 'U';
	case V = 'V';
	case W = 'W';
	case X = 'X';
	case Y = 'Y';
	case Z = 'Z';
	case L1 = 'L1';
	case M1 = 'M1';
	case M2 = 'M2';
	case O1 = 'O1';
	case V1 = 'V1';
	case ZO = 'ZO';

    public function getLabel(): string
    {
        return match($this) {
			self::A => 'A',
			self::B => 'B',
			self::C => 'C',
			self::D => 'D',
			self::E => 'E',
			self::G => 'G',
			self::H => 'H',
			self::I => 'I',
			self::L => 'L',
			self::M => 'M',
			self::N => 'N',
			self::O => 'O',
			self::P => 'P',
			self::Q => 'Q',
			self::R => 'R',
			self::S => 'S',
			self::T => 'T',
			self::U => 'U',
			self::V => 'V',
			self::W => 'W',
			self::X => 'X',
			self::Y => 'Y',
			self::Z => 'Z',
			self::L1 => 'L1',
			self::M1 => 'M1',
			self::M2 => 'M2',
			self::O1 => 'O1',
			self::V1 => 'V1',
			self::ZO => 'ZO',

            default => null,
        };
    }
}
