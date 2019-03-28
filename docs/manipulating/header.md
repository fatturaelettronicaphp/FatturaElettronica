# Instestazione della Fattura Elettronica

L'instestazione della fattura elettronica è direttamente accessibile dall'oggetto `DigitalDocument`.

Ogni campo dell'XML viene mappato al valore correto, forzandone la tipizzazione.

## Cessionario / Committente (o Cliente)
``` php
$xml = '/path/to/file.xml';
$eDocument = \FatturaElettronicaPhp\FatturaElettronica\DigitalDocument::parseFrom($xml);

/** @var \FatturaElettronicaPhp\FatturaElettronica\Customer $customer **/
$customer = $eDocument->getCustomer();
``` 

### Dati Anagrafici del Cliente
```php
/** @var string $nomeOrganizzazione */
$nomeOrganizzazione = $customer->getOrganization();
/** @var string $partitaIva */
$partitaIva = $customer->getVatNumber();
```
 
