# Lettura di una Fattura Elettronica


## Da un file XML
``` php
$xml = '/path/to/file.xml';
$eDocument = DigitalDocument::parseFrom($xml);

/** @var \Weble\FatturaElettronica\Customer $customer **/
$customer = $digitalDocument->getCustomer();

/** @var \Weble\FatturaElettronica\Supplier $supplier **/
$supplier = $digitalDocument->getSupplier();

/** @var \Weble\FatturaElettronica\DigitalDocument[] $documents **/
$documents = $digitalDocument->getDocumentInstances();

$customer->getOrganization(); // Alpha Srl
$customer->getVatNumber(); // 03412317712

/** @var \Weble\FatturaElettronica\DigitalDocument $document **/
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