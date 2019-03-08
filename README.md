# Fattura Elettronica

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/FatturaElettronica.svg?style=flat-square)](https://packagist.org/packages/spatie/:package_name)
[![Build Status](https://img.shields.io/travis/spatie/FatturaElettronica/master.svg?style=flat-square)](https://travis-ci.org/spatie/:package_name)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/FatturaElettronica.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/:package_name)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/FatturaElettronica.svg?style=flat-square)](https://packagist.org/packages/spatie/:package_name)

Pacchetto per la lettura e la generazione della fattura elettronica, sia PA che B2B / B2C

## Installation

You can install the package via composer:

```bash
composer require weble/FatturaElettronica
```

## Usage

``` php
$documentParser = new DigitalDocumentParser($file); // filepath to xml or p7m file
$digitalDocument = $documentParser->parse();

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
