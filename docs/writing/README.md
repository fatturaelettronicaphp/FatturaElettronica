# Scrittura

## Scrivere un File XML
``` php
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

$eDocument->write($filePath);
```

## SimpleXmlElement
``` php
// This needs to be a DigitalDocumentInterface object
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

/** @var \SimpleXmlElement $xml **/
$xml = $eDocument->serialize();
```