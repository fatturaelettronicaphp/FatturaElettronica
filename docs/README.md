# Fattura Elettronica

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fatturaelettronicaphp/fattura-elettronica.svg?style=flat-square)](https://packagist.org/packages/fatturaelettronicaphp/fattura-elettronica)
[![Build Status](https://img.shields.io/travis/fatturaelettronicaphp/fattura-elettronica/master.svg?style=flat-square)](https://travis-ci.org/fatturaelettronicaphp/fattura-elettronica)
[![Total Downloads](https://img.shields.io/packagist/dt/fatturaelettronicaphp/fattura-elettronica.svg?style=flat-square)](https://packagist.org/packages/fatturaelettronicaphp/fattura-elettronica)

Pacchetto PHP per la lettura, la generazione e la validazione della fattura elettronica, sia per la Pubblica Amministrazione che tra privati (B2B)


## Installazione

Il pacchetto viene installato attraverso composer, e richiede PHP >= 7.1

```bash
composer require fatturaelettronicaphp/fattura-elettronica
```

## Utilizzo

### Lettura
``` php
// $xml può essere un file xml, p7m o un'istanza di \SimpleXmlElement
$eDocument = DigitalDocument::parseFrom($xml);

$customer = $digitalDocument->getCustomer();
$supplier = $digitalDocument->getSupplier();
$documents = $digitalDocument->getDocumentInstances();
...

$customer->getOrganization();
$customer->getVatNumber(); 
...

$documents[0]->getDocumentDate();
$documents[0]->getDocumentNumber();
...
```

### Scrittura
``` php
// Deve essere un'istanza di \FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface 
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

....

// Oggetto \SimpleXmlElement
$xml = $eDocument->serialize();

// Scrive direttamente il file XML
$eDocument->write($filePath);
```

## Testing

```bash
composer test
```
