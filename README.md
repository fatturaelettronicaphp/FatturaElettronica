# Fattura Elettronica

[![Build Status](https://travis-ci.com/Weble/FatturaElettronica.svg?token=dkUekxQMLMKLsPhqsxiT&branch=master)](https://travis-ci.com/Weble/FatturaElettronica)

Pacchetto per la lettura e la generazione della fattura elettronica, sia PA che B2B / B2C

## Installation

You can install the package via composer:

```bash
composer require weble/FatturaElettronica
```

## Usage

### Parsing
``` php
// this can be an xml file, a p7m file, or an instance of SimpleXmlElement
$documentParser = new DigitalDocumentParser($file);
$eDocument = $documentParser->parse();

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

### Writing
``` php
// This needs to be a DigitalDocumentInterface object
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

....

// This is a SimpleXmlElement
$xml = $writer->generate()->xml();

// This writes to an XML file
$writer->generate()->write($filePath);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email daniele@weble.it instead of using the issue tracker.


## Credits

- [Daniele Rosario](https://github.com/Skullbock)
- [Tobia Zanarella](https://github.com/ShellrentSrl)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
