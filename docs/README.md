# Fattura Elettronica

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fatturaelettronicaphp/fattura-elettronica.svg?style=flat-square)](https://packagist.org/packages/fatturaelettronicaphp/fattura-elettronica)
[![Build Status](https://img.shields.io/travis/fatturaelettronicaphp/FatturaElettronica/master.svg?style=flat-square)](https://travis-ci.org/fatturaelettronicaphp/FatturaElettronica)

Pacchetto PHP per la lettura, la generazione e la validazione della fattura elettronica, sia per la Pubblica Amministrazione che tra privati (B2B)

Il pacchetto è utilizzato per gestione delle fatture elettroniche nel portale [https://www.shellrent.com/fattura-elettronica/](https://www.shellrent.com/fattura-elettronica/?utm_source=fatturaelettronicaphp)

## Sponsors
<!--special start-->
 
<p>
  <a href="https://www.weble.it" target="_blank">
    <img width="200" src="./assets/weble-logo-quadrato.png">
  </a>
  <a href="https://www.shellrent.com" target="_blank">
      <img height="220" src="./assets/shellrent.jpg">
    </a>
</p>
  
<!--special end-->


## Installazione

Il pacchetto viene installato attraverso composer, e richiede PHP >= 7.3

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
