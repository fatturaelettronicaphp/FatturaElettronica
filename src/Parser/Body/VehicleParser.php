<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class VehicleParser extends RelatedDocumentParser
{
    protected function performParsing ()
    {
        $value = $this->extractValueFromXml('DatiVeicoli/Data');
        $this->digitalDocymentInstance->setVehicleRegistrationDate($value);

        $value = $this->extractValueFromXml('DatiVeicoli/TotalePercorso');
        $this->digitalDocymentInstance->setVehicleTotalKm($value);
    }
}
