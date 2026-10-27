# Validazione

La libreria valida il file XML usando lo [Schema Ufficiale dell'Agenzia delle Entrate](https://www.fatturapa.gov.it/it/norme-e-regole/documentazione-fattura-elettronica/formato-fatturapa/).

## Controllare la validità del documento
``` php
$xml = '/path/to/file.xml';
$eDocument = \Weble\FatturaElettronica\DigitalDocument::parseFrom($xml);

// true / False
$eDocument->isValid();
```

### Controllare eventuali errori
``` php
$xml = '/path/to/file.xml';
$eDocument = \Weble\FatturaElettronica\DigitalDocument::parseFrom($xml);

$errors = $eDocument->validate()->errors();
//  [
//    '/path/to/xml/wrong/field' => [
//         'First Error',
//         'Second Error'
//    ]
// ]
```
