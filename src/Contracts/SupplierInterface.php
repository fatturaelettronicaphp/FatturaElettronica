<?php

namespace Weble\FatturaElettronica\Contracts;

use DateTime;
use Weble\FatturaElettronica\Enums\AssociateType;
use Weble\FatturaElettronica\Enums\WoundUpType;
use Weble\FatturaElettronica\Enums\TaxRegime;

interface SupplierInterface extends BillablePersonInterface
{
    public function getRegisterState ();

    public function setRegister ($register);

    public function getEmail ();

    public function setEmail ($email);

    public function getAssociateType (): ?AssociateType;

    public function setTaxRegime ($taxRegime);

    public function getRegister ();

    public function setPhone ($phone);

    public function getRegisterNumber ();

    public function setRegisterDate ($registerDate, $format = null);

    public function getTaxRegime (): TaxRegime;

    public function setAdministrativeContact ($administrativeContact);

    public function setReaOffice ($reaOffice);

    public function getReaOffice ();

    public function getFax ();

    public function setReaNumber ($reaNumber);

    public function getReaNumber ();

    public function getSettlementType (): ?WoundUpType;

    public function getRegisterDate (): ?DateTime;

    public function setSettlementType ($settlementType);

    public function getAdministrativeContact ();

    public function setRegisterState ($registerState);

    public function setRegisterNumber ($registerNumber);

    public function getCapital ();

    public function getPhone ();

    public function setFax ($fax);

    public function setCapital ($capital);

    public function setAssociateType ($associateType);
}