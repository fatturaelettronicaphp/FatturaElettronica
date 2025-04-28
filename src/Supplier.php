<?php

namespace FatturaElettronicaPhp\FatturaElettronica;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\SupplierInterface;
use FatturaElettronicaPhp\FatturaElettronica\Enums\AssociateType;
use FatturaElettronicaPhp\FatturaElettronica\Enums\TaxRegime;
use FatturaElettronicaPhp\FatturaElettronica\Enums\WoundUpType;

class Supplier extends BillablePerson implements SupplierInterface
{
    /** @var string */
    protected $register;
    /** @var string */
    protected $registerState;
    /** @var string */
    protected $registerNumber;
    /** @var DateTime */
    protected $registerDate;
    /** @var TaxRegime */
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
    /** @var WoundUpType */
    protected $settlementType;

    public function __construct()
    {
        $this->taxRegime = TaxRegime::RF01;
    }

    public function getReaOffice()
    {
        return $this->reaOffice;
    }

    public function setReaOffice($reaOffice): self
    {
        $this->reaOffice = $reaOffice;

        return $this;
    }

    public function getReaNumber()
    {
        return $this->reaNumber;
    }

    public function setReaNumber($reaNumber): self
    {
        $this->reaNumber = $reaNumber;

        return $this;
    }

    public function getCapital()
    {
        return $this->capital;
    }

    public function setCapital($capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function getAssociateType(): ?AssociateType
    {
        return $this->associateType;
    }

    public function setAssociateType($associateType): self
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

    public function getSettlementType(): ?WoundUpType
    {
        return $this->settlementType;
    }

    public function setSettlementType($settlementType): self
    {
        if ($settlementType === null) {
            return $this;
        }

        if (!$settlementType instanceof WoundUpType) {
            $settlementType = WoundUpType::from($settlementType);
        }
        $this->settlementType = $settlementType;

        return $this;
    }

    public function getAdministrativeContact()
    {
        return $this->administrativeContact;
    }

    public function setAdministrativeContact($administrativeContact): self
    {
        $this->administrativeContact = $administrativeContact;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFax()
    {
        return $this->fax;
    }

    public function setFax($fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getRegister()
    {
        return $this->register;
    }

    public function setRegister($register): self
    {
        $this->register = $register;

        return $this;
    }

    public function getRegisterState()
    {
        return $this->registerState;
    }

    public function setRegisterState($registerState): self
    {
        $this->registerState = $registerState;

        return $this;
    }

    public function getRegisterNumber()
    {
        return $this->registerNumber;
    }

    public function setRegisterNumber($registerNumber): self
    {
        $this->registerNumber = $registerNumber;

        return $this;
    }

    public function getRegisterDate(): ?DateTime
    {
        return $this->registerDate;
    }

    public function setRegisterDate($registerDate, $format = null): self
    {
        if ($registerDate === null) {
            return $this;
        }

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

    public function getTaxRegime(): TaxRegime
    {
        return $this->taxRegime;
    }

    public function setTaxRegime($taxRegime): self
    {
        if (!$taxRegime instanceof TaxRegime) {
            $taxRegime = TaxRegime::from($taxRegime);
        }

        $this->taxRegime = $taxRegime;

        return $this;
    }

    public function hasContacts(): bool
    {
        return $this->getPhone() || $this->getEmail() || $this->getFax();
    }
}
