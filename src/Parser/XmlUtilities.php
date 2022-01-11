<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Parser;

use SimpleXMLElement;

trait XmlUtilities
{
    abstract public function xml(): SimpleXMLElement;

    protected function extractAttributeFromXmlElement(SimpleXMLElement $xml, string $attribute): ?string
    {
        return $xml->attributes()[$attribute] ??  null;
    }

    protected function extractValueFromXml(string $xPath, $convertToString = true)
    {
        return $this->extractValueFromXmlElement($this->xml(), $xPath, $convertToString);
    }

    protected function extractValueFromXmlElement(SimpleXMLElement $xml, string $xPath, $convertToString = true)
    {
        $value = $xml->xpath($xPath);

        if (empty($value)) {
            return null;
        }

        if (count($value) <= 0) {
            return null;
        }

        if ($convertToString) {
            return trim($value[0]->__toString());
        }

        return $value;
    }
}
