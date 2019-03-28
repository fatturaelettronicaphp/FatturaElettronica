# Lettura di una Fattura Elettronica


## Da un file XML
``` php
$xml = '/path/to/file.xml';
$eDocument = DigitalDocument::parseFrom($xml);

/** @var \FatturaElettronicaPhp\FatturaElettronica\Customer $customer **/
$customer = $digitalDocument->getCustomer();

/** @var \FatturaElettronicaPhp\FatturaElettronica\Supplier $supplier **/
$supplier = $digitalDocument->getSupplier();

/** @var \FatturaElettronicaPhp\FatturaElettronica\DigitalDocument[] $documents **/
$documents = $digitalDocument->getDocumentInstances();

$customer->getOrganization(); // Alpha Srl
$customer->getVatNumber(); // 03412317712

/** @var \FatturaElettronicaPhp\FatturaElettronica\DigitalDocument $document **/
foreach ($documents as $document) {
    $document->getDocumentDate(); // \DateTime
    $document->getDocumentNumber(); // 123
}

```


## Da un file p7m
``` php
$p7m = '/path/to/file.xml.p7m';
$eDocument = DigitalDocument::parseFrom($p7m);
```


## Da un elemento SimpleXMLElement
``` php
$xml = simplexml_load_string($stringXmlFatturaElettronica);
$eDocument = DigitalDocument::parseFrom($xml);
```