# Fattura Elettronica

[![Build Status](https://travis-ci.com/Weble/FatturaElettronica.svg?token=dkUekxQMLMKLsPhqsxiT&branch=master)](https://travis-ci.com/Weble/FatturaElettronica)

Pacchetto per la lettura e la generazione della fattura elettronica, sia PA che B2B / B2C

## Installazione

Il pacchetto può essere installato tramite `composer`

```bash
composer require weble/FatturaElettronica
```

## Utilizzo

### Lettura
``` php
// $xml can be an xml file, a p7m file, or an instance of SimpleXmlElement
$eDocument = DigitalDocument::parseFrom($xml);

$customer = $digitalDocument->getCustomer();
$supplier = $digitalDocument->getSupplier();
$documents = $digitalDocument->getDocumentInstances();
...

$customer->getOrganization();  // Same for supplier
$customer->getVatNumber(); // Same for supplier
...

$documents[0]->getDocumentDate();
$documents[0]->getDocumentNumber();
...
```

### Scrittura
``` php
// This needs to be a DigitalDocumentInterface object
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

....

// This is a SimpleXmlElement
$xml = $eDocument->serialize();

// This writes to an XML file
$eDocument->write($filePath);
```

## Testing

```bash
composer test
```
