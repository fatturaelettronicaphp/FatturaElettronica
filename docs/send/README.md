# Invio ad intermediario

La libreria permette l'invio di una fattura direttamente ai sistemi che si occupano di convalidarla, firmarla e inviarla al Sistema di interscambio (**intermediari**).

Al momento i servizi supportati sono:
- Acube
- Aruba

## Inviare una fattura
``` php
$file      = __DIR__ . '/fixtures/IT01234567899_000sq.xml';
$xml       = simplexml_load_file($file);
$eDocument = DigitalDocument::parseFrom($xml);
$acube = new AcubeSender('YOUR_USERNAME','YOUR_PASSWORD');
$aruba = new ArubaSender('YOUR_USERNAME','YOUR_PASSWORD');

if($eDocument->isValid()){
    $acube->send($eDocument);
    $aruba->send($eDocument);
} else {
    $errors = $eDocument->validate()->errors();
}
```

## Environment
Le classi "_sender_" hanno al loro interno un "environment" di default inizializzato a **development**,
questo comporta che gli endpoint che chiameranno le varie classi sono quelli di test e non quelli reali.
Una volta messo online il vostro progetto dovrete chiamare la classe Sender impostandogli l'environment a production come nel seguente esempio:

NB. **state molto attenti quando chiamerete le classi con production, la libreria invierà agli endpoint reali la vostra fattura**
``` php
$aruba = new ArubaSender('YOUR_USERNAME','YOUR_PASSWORD');
$aruba->setProduction(); //setDevelopment() per farlo tornare in ambiente di test
if($eDocument->isValid()){
    $aruba->send($eDocument);
} else {
    $errors = $eDocument->validate()->errors();
}
```
 