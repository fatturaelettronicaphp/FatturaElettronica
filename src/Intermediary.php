<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\IntermediaryInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Intermediary extends BillablePerson implements ArrayableInterface, IntermediaryInterface
{
}
