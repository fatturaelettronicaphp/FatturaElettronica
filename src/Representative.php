<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\RepresentativeInterface;
use FatturaElettronicaPhp\FatturaElettronica\Utilities\ArrayableInterface;

class Representative extends BillablePerson implements ArrayableInterface, RepresentativeInterface
{
}
