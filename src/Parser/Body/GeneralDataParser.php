<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser\Body;

use DateTime;
use FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentType;
use FatturaElettronicaPhp\FatturaElettronica\Exceptions\InvalidXmlFile;

class GeneralDataParser extends AbstractBodyParser
{
    protected function performParsing ()
    {
        $types = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/TipoDocumento');
        if ($types === null) {
            throw new InvalidXmlFile('<TipoDocumento> not found');
        }
        $type = DocumentType::from($types);

        $datas = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Data');
        if ($datas === null) {
            throw new InvalidXmlFile('<Data> not found');
        }
        $data = new DateTime($datas);

        $value = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Divisa');
        if ($datas === null) {
            throw new InvalidXmlFile('<Divisa> not found');
        }

        $this->digitalDocymentInstance->setCurrency($value);

        $number = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Numero');
        if ($number === null) {
            throw new InvalidXmlFile('<Numero> not found');
        }

        $descriptions = (array)$this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Causale', false);
        foreach ($descriptions as $description) {
            $this->digitalDocymentInstance->addDescription($description);
        }

        $documentTotal = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/ImportoTotaleDocumento');
        $rounding = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Arrotondamento');
        $art73 = $this->extractValueFromXml('DatiGenerali/DatiGeneraliDocumento/Art73');

        $this->digitalDocymentInstance
            ->setDocumentType($type)
            ->setDocumentDate($data)
            ->setDocumentNumber($number)
            ->setDocumentTotal($documentTotal)
            ->setArt73($art73)
            ->setRounding($rounding);
    }
}
