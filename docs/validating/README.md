# Validazione

La libreria valida il file XML usando lo [Schema Ufficiale dell'Agenzia delle Entrate](https://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2.1/Schema_del_file_xml_FatturaPA_versione_1.2.1.xsd).

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
