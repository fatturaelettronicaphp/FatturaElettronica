<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Contracts;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Enums\AssociateType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\WoundUpType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TaxRegime;

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

    public function hasContacts(): bool;
}