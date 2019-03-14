# Utilizzo

## Dati Intestazione
``` php
$xml = '/path/to/file.xml';
$eDocument = DigitalDocument::parseFrom($xml);

/** @var \Weble\FatturaElettronica\Customer $customer **/
$customer = $digitalDocument->getCustomer();

/** @var \Weble\FatturaElettronica\Supplier $supplier **/
$supplier = $digitalDocument->getSupplier();

/** @var \Weble\FatturaElettronica\DigitalDocument[] $documents **/
$documents = $digitalDocument->getDocumentInstances();

$customer->getOrganization();
$customer->getVatNumber();

```

### Customer
``` php
$xml = '/path/to/file.xml';
$eDocument = DigitalDocument::parseFrom($xml);

/** @var \Weble\FatturaElettronica\Customer $customer **/
$customer = $digitalDocument->getCustomer();

$customer->getOrganization();
$customer->getVatNumber();

```

### Supplier
``` php
$xml = '/path/to/file.xml';
$eDocument = DigitalDocument::parseFrom($xml);

/** @var \Weble\FatturaElettronica\Supplier $customer **/
$supplier = $digitalDocument->getSupplier();

$supplier->getOrganization();
$supplier->getVatNumber();

```

## Dati Documento
``` php
$xml = '/path/to/file.xml';
$eDocument = DigitalDocument::parseFrom($xml);

/** @var \Weble\FatturaElettronica\DigitalDocument $document **/
foreach ($documents as $document) {
    $document->getDocumentDate();
    $document->getDocumentNumber();
}
```
