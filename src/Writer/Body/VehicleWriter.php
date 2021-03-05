<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Writer\Body;

class VehicleWriter extends AbstractBodyWriter
{
    protected function performWrite()
    {
        if ($this->body->getVehicleRegistrationDate() !== null || $this->body->getVehicleTotalKm() !== null) {
            $vehicleData = $this->xml->addChild('DatiVeicoli');

            $value = $this->body->getVehicleRegistrationDate();
            if ($value !== null) {
                $vehicleData->addChild('Data', $value->format('Y-m-d'));
            }

            $value = $this->body->getVehicleTotalKm();
            if ($value !== null) {
                $vehicleData->addChild('TotalePercorso', $value);
            }
        }
    }
}
