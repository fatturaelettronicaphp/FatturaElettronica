# Scrittura

## Scrivere un File XML
``` php
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

$eDocument->write($filePath);
```

## SimpleXmlElement
``` php
// Deve essere un'istanza di \FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface 
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

/** @var \SimpleXmlElement $xml **/
$xml = $eDocument->serialize();
```