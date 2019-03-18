# Utilizzo

## Dati Intestazione
``` php
$xml = '/path/to/file.xml';
$eDocument = \Weble\FatturaElettronica\DigitalDocument::parseFrom($xml);

/** @var \Weble\FatturaElettronica\Customer $customer **/
$customer = $eDocument->getCustomer();

/** @var \Weble\FatturaElettronica\Supplier $supplier **/
$supplier = $eDocument->getSupplier();

/** @var \Weble\FatturaElettronica\DigitalDocument[] $documents **/
$documents = $eDocument->getDocumentInstances();

$customer->getOrganization();
$customer->getVatNumber();

```

### Cessionario / Committente (o Cliente)
``` php
$xml = '/path/to/file.xml';
$eDocument = \Weble\FatturaElettronica\DigitalDocument::parseFrom($xml);

/** @var \Weble\FatturaElettronica\Customer $customer **/
$customer = $eDocument->getCustomer();

$customer->getName();
$customer->getVatNumber();
$customer->getSurname();
$customer->getOrganization();
$customer->getCountryCode();
$customer->getFiscalCode ();
$customer->getTitle();
$customer->getEori();
$customer->getRepresentative();
```

### Supplier
``` php
$xml = '/path/to/file.xml';
$eDocument = \Weble\FatturaElettronica\DigitalDocument::parseFrom($xml);

/** @var \Weble\FatturaElettronica\Supplier $customer **/
$supplier = $eDocument->getSupplier();

$supplier->getOrganization();
$supplier->getVatNumber();

```

## Dati Documento
``` php
$xml = '/path/to/file.xml';
$eDocument = \Weble\FatturaElettronica\DigitalDocument::parseFrom($xml);

$documents = $eDocument->getDocumentInstances();

/** @var \Weble\FatturaElettronica\DigitalDocumentInstance $document **/
foreach ($documents as $document) {
    $document->getDocumentDate();
    $document->getDocumentNumber();
}
```
