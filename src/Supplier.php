<?php

namespace Weble\FatturaElettronica;

use Weble\FatturaElettronica\Enums\TaxRegime;
use Weble\FatturaElettronica\Enums\AssociateType;
use Weble\FatturaElettronica\Enums\SettlementType;
use DateTime;

class Supplier extends Billable
{
    /** @var string */
    protected $register;
    /** @var string */
    protected $registerState;
    /** @var string */
    protected $registerNumber;
    /** @var \DateTime */
    protected $registerDate;
    /** @var \Weble\FatturaElettronica\Enums\TaxRegime */
    protected $taxRegime;
    /** @var string */
    protected $email;
    /** @var string */
    protected $phone;
    /** @var string */
    protected $fax;
    /** @var string */
    protected $administrativeContact;
    /** @var string */
    protected $reaOffice;
    /** @var string */
    protected $reaNumber;
    /** @var string */
    protected $capital;
    /** @var AssociateType */
    protected $associateType;
    /** @var SettlementType */
    protected $settlementType;

    public function getReaOffice ()
    {
        return $this->reaOffice;
    }

    public function setReaOffice ($reaOffice): Supplier
    {
        $this->reaOffice = $reaOffice;
        return $this;
    }

    public function getReaNumber ()
    {
        return $this->reaNumber;
    }

    public function setReaNumber ($reaNumber): Supplier
    {
        $this->reaNumber = $reaNumber;
        return $this;
    }

    public function getCapital ()
    {
        return $this->capital;
    }

    public function setCapital ($capital): Supplier
    {
        $this->capital = $capital;
        return $this;
    }

    public function getAssociateType (): AssociateType
    {
        return $this->associateType;
    }

    public function setAssociateType ($associateType): Supplier
    {
        if ($associateType === null) {
            return $this;
        }

        if (!$associateType instanceof AssociateType) {
            $associateType = AssociateType::from($associateType);
        }

        $this->associateType = $associateType;
        return $this;
    }

    public function getSettlementType (): SettlementType
    {
        return $this->settlementType;
    }

    public function setSettlementType ($settlementType): Supplier
    {
        if ($settlementType === null) {
            return $this;
        }

        if (!$settlementType instanceof SettlementType) {
            $settlementType = SettlementType::from($settlementType);
        }
        $this->settlementType = $settlementType;
        return $this;
    }

    public function getAdministrativeContact ()
    {
        return $this->administrativeContact;
    }

    public function setAdministrativeContact ($administrativeContact): Supplier
    {
        $this->administrativeContact = $administrativeContact;
        return $this;
    }

    public function getEmail ()
    {
        return $this->email;
    }

    public function setEmail ($email): Supplier
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone ()
    {
        return $this->phone;
    }

    public function setPhone ($phone): Supplier
    {
        $this->phone = $phone;
        return $this;
    }

    public function getFax ()
    {
        return $this->fax;
    }

    public function setFax ($fax): Supplier
    {
        $this->fax = $fax;
        return $this;
    }

    public function getRegister ()
    {
        return $this->register;
    }

    public function setRegister ($register): Supplier
    {
        $this->register = $register;
        return $this;
    }

    public function getRegisterState ()
    {
        return $this->registerState;
    }

    public function setRegisterState ($registerState): Supplier
    {
        $this->registerState = $registerState;
        return $this;
    }

    public function getRegisterNumber ()
    {
        return $this->registerNumber;
    }

    public function setRegisterNumber ($registerNumber): Supplier
    {
        $this->registerNumber = $registerNumber;
        return $this;
    }

    public function getRegisterDate ()
    {
        return $this->registerDate;
    }

    public function setRegisterDate ($registerDate, $format = null): Supplier
    {
        if ($registerDate === null) {
            return $this;
        }

        if (!$registerDate instanceof DateTime) {
            if ($format) {
                $registerDate = DateTime::createFromFormat($format, $registerDate);
            } else {
                $registerDate = new DateTime();
            }
        }

        $this->registerDate = $registerDate;
        return $this;
    }

    public function getTaxRegime (): TaxRegime
    {
        return $this->taxRegime;
    }

    public function setTaxRegime ($taxRegime): Supplier
    {
        if (!$taxRegime instanceof TaxRegime) {
            $taxRegime = TaxRegime::from($taxRegime);
        }

        $this->taxRegime = $taxRegime;
        return $this;
    }


}
