# Scrittura

## Scrivere un File XML
``` php
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

$eDocument->write($filePath);
```

Se `$filePath` è un percorso ad un file xml completo (es: `\path\to\xml\file.xml`) il file verrà scritto all'interno
di quel nome file.
Se `$filePath` è una directory, il file verrà scritto all'interno della directory specificata, utilizzato il nome del
file richiesto dall'SDI (es: `\path\to\your\folder\IT012345678910_00000.xml`)

## SimpleXmlElement
``` php
// Deve essere un'istanza di \FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface 
$eDocument = new DigitalDocument();
$eDocument->setTransmissionFormat('FPR12');

/** @var \SimpleXmlElement $xml **/
$xml = $eDocument->serialize();
```