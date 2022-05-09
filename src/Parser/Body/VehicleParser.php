<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

class VehicleParser extends RelatedDocumentParser
{
    protected function performParsing()
    {
        $value = $this->extractValueFromXml('DatiVeicoli/Data');
        $this->digitalDocumentInstance->setVehicleRegistrationDate($value);

        $value = $this->extractValueFromXml('DatiVeicoli/TotalePercorso');
        $this->digitalDocumentInstance->setVehicleTotalKm($value);
    }
}
