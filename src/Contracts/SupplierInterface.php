<?php

namespace Weble\FatturaElettronica\Contracts;


use DateTime;
use Weble\FatturaElettronica\Enums\AssociateType;
use Weble\FatturaElettronica\Enums\SettlementType;
use Weble\FatturaElettronica\Enums\TaxRegime;

interface SupplierInterface extends BillableInterface
{
    public function getRegisterState ();

    public function setRegister ($register): SupplierInterface;

    public function getEmail ();

    public function setEmail ($email): SupplierInterface;

    public function getAssociateType (): ?AssociateType;

    public function setTaxRegime ($taxRegime): SupplierInterface;

    public function getRegister ();

    public function setPhone ($phone): SupplierInterface;

    public function getRegisterNumber ();

    public function setRegisterDate ($registerDate, $format = null): SupplierInterface;

    public function getTaxRegime (): TaxRegime;

    public function setAdministrativeContact ($administrativeContact): SupplierInterface;

    public function setReaOffice ($reaOffice): SupplierInterface;

    public function getReaOffice ();

    public function getFax ();

    public function setReaNumber ($reaNumber): SupplierInterface;

    public function getReaNumber ();

    public function getSettlementType (): ?SettlementType;

    public function getRegisterDate (): ?DateTime;

    public function setSettlementType ($settlementType): SupplierInterface;

    public function getAdministrativeContact ();

    public function setRegisterState ($registerState): SupplierInterface;

    public function setRegisterNumber ($registerNumber): SupplierInterface;

    public function getCapital ();

    public function getPhone ();

    public function setFax ($fax): SupplierInterface;

    public function setCapital ($capital): SupplierInterface;

    public function setAssociateType ($associateType): SupplierInterface;
}