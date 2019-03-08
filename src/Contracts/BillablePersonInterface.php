<?php

namespace Weble\FatturaElettronica\Contracts;

interface BillablePersonInterface extends BillableInterface
{
    public function getFiscalCode ();

    public function setFiscalCode ($fiscalCode);

    public function setTitle ($title);

    public function setEori ($eori);

    public function getTitle ();

    public function getEori ();
}