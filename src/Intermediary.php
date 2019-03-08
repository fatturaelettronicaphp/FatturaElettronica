<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Contracts\AddressInterface;
use Weble\FatturaElettronica\Contracts\BillableInterface;
use Weble\FatturaElettronica\Contracts\IntermediaryInterface;
use Weble\FatturaElettronica\Contracts\RepresentativeInterface;
use Weble\FatturaElettronica\Utilities\Arrayable;
use Weble\FatturaElettronica\Utilities\ArrayableInterface;

class Intermediary extends BillablePerson implements ArrayableInterface, IntermediaryInterface
{

}
