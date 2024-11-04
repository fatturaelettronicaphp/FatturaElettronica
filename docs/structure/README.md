# Struttura del pacchetto Fattura Elettronica

Il pacchetto contiene una struttura di classi costruita sulla base del tracciato XML Fattura Elettronica così come ufficialmente documentato dall'Agenzia delle Entrate: [Documentazione FatturaPA Agenzia delle Entrate](https://www.fatturapa.gov.it/it/norme-e-regole/normativa/) Il riferimento ufficiale utilizzato per la lettura e la scrittura di file XML è: `Rappresentazione tabellare del tracciato FatturaPA versione 1.2.1.xls`.

## Dati enumerabili

L'XML della fattura elettronica prevede diversi dati enumerabili: alcuni sono indicati in apposite tabelle (`RegimeFiscale`, `TipoCassa`, ecc.), altri sono invece riportati nei campi in cui vengono utilizzati (tipo sconto/maggiorazione, quantità di soci, ecc.).

Ognuno di questi dati è tradotto in una apposita classe `Enum` nel namespace `FatturaElettronicaPhp\FatturaElettronica\Enums`.

- Socio unico/più soci: `FatturaElettronicaPhp\FatturaElettronica\Enums\AssociateType`
- Tipo cessione/prestazione: `FatturaElettronicaPhp\FatturaElettronica\Enums\CancelType`
- Tipo ritenuta: `FatturaElettronicaPhp\FatturaElettronica\Enums\DeductionType`
- Tipo sconto/maggiorazione: `FatturaElettronicaPhp\FatturaElettronica\Enums\DiscountType`
- Formato documento: `FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentFormat`
- Tipo documento: `FatturaElettronicaPhp\FatturaElettronica\Enums\DocumentType`
- Individuazione soggetto emittente: `FatturaElettronicaPhp\FatturaElettronica\Enums\EmittingSubject`
- Tipo cassa previdenziale: `FatturaElettronicaPhp\FatturaElettronica\Enums\FundType`
- Metodo di pagamento: `FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentMethod`
- Termini di pagamento: `FatturaElettronicaPhp\FatturaElettronica\Enums\PaymentTerm`
- Codici destinatario di default (cliente estero o codice destinatario B2B/B2C sconosciuto o non presente): `FatturaElettronicaPhp\FatturaElettronica\Enums\RecipientCode`
- Regime fiscale: `FatturaElettronicaPhp\FatturaElettronica\Enums\TaxRegime`
- Formato trasmissione: `FatturaElettronicaPhp\FatturaElettronica\Enums\TransmissionFormat`
- Esigibilità IVA: `FatturaElettronicaPhp\FatturaElettronica\Enums\VatEligibility`
- Tipologia di Natura IVA: `FatturaElettronicaPhp\FatturaElettronica\Enums\VatNature`
- Stato liquidazione: `FatturaElettronicaPhp\FatturaElettronica\Enums\WoundUpType`

## Struttura base

La classe di base da utilizzare è `FatturaElettronicaPhp\FatturaElettronica\DigitalDocument`, che rappresenta un intero file XML (o p7m) di una Fattura Elettronica.

``` php
$eDocument = \FatturaElettronicaPhp\FatturaElettronica\DigitalDocument::parseFrom( $xml );
```

Un'istanza di `FatturaElettronicaPhp\FatturaElettronica\DigitalDocument` contiene essenzialmente le due parti principali di un XML:

- `1 <FatturaElettronicaHeader>`
- `2 <FatturaElettronicaBody>`

### Header

I dati dell'intestazione del documento elettronico sono espressi proprietà della classe `FatturaElettronicaPhp\FatturaElettronica\DigitalDocument` con i relativi metodi le lettura e scrittura (get/set/add).

- `1.1 <DatiTrasmissione>`
  - I dati relativi a questo nodo sono espressi in metodi disponibili nella classe `DigitalDocument`
- `1.2 <CedentePrestatore>` (o Fornitore)
  - Istanza di `FatturaElettronicaPhp\FatturaElettronica\Supplier`
- `1.3 <RappresentanteFiscale>`
  - Istanza di `FatturaElettronicaPhp\FatturaElettronica\Customer`
- `1.4 <CessionarioCommittente>`
  - Istanza di `FatturaElettronicaPhp\FatturaElettronica\BillablePerson`
- `1.5 <TerzoIntermediarioOSoggettoEmittente>`
  - Istanza di `FatturaElettronicaPhp\FatturaElettronica\Intermediary`
- `1.6 <SoggettoEmittente>`
  - Istanza di `FatturaElettronicaPhp\FatturaElettronica\BillablePerson`

La classe `FatturaElettronicaPhp\FatturaElettronica\Address` rappresenta un Indirizzo utilizzato in ognuna delle classi sopra citate.

### Corpo fattura

Ogni file XML può contenere una o più fatture elettroniche grazie alla presenza di uno o più nodi `<FatturaElettronicaBody>`. Quindi l'istanza di `FatturaElettronicaPhp\FatturaElettronica\DigitalDocument` contiene una collezione di `FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance`. Ognuno di essi rappresenta appunto un `<FatturaElettronicaBody>`.

I dati presenti in `2.1.1 <DatiGeneraliDocumento>` sono rappresentati in proprietà disponibili in `FatturaElettronicaPhp\FatturaElettronica\DigitalDocumentInstance`. Alcuni di essi sono collezioni di istanze di altri oggetti che a loro volta contengono i dati previsti dall'XML.

Le linee del corpo fattura (`2.2 <DatiBeniServizi>`) sono rappresentate dalla collezione di istanze di `FatturaElettronicaPhp\FatturaElettronica\Line`.

- `FatturaElettronicaPhp\FatturaElettronica\Fund`
  - `2.1.1.7 <DatiCassaPrevidenziale>`
- `FatturaElettronicaPhp\FatturaElettronica\Discount`, utilizzata per esprimere uno o più sconti (collezione) per i nodi:
  - `2.1.1.8 <ScontoMaggiorazione>`
  - `2.2.1.10 <ScontoMaggiorazione>`
- `FatturaElettronicaPhp\FatturaElettronica\RelatedDocument`
  - `2.1.3 <DatiContratto>`
  - `2.1.4 <DatiConvenzione>`
  - `2.1.5 <DatiRicezione>`
  - `2.1.6 <DatiFattureCollegate>`
- `FatturaElettronicaPhp\FatturaElettronica\ShippingLabel`
  - `2.1.8 <DatiDDT>`
- `FatturaElettronicaPhp\FatturaElettronica\Shipment`
  - `2.1.9 <DatiTrasporto>`
- `FatturaElettronicaPhp\FatturaElettronica\Total`
  - `2.2.2 <DatiRiepilogo>`
- `FatturaElettronicaPhp\FatturaElettronica\PaymentInfo`
  - `2.4 <DatiPagamento>`
  - A sua volta contiene una o più istanze di `FatturaElettronicaPhp\FatturaElettronica\PaymentDetails` che corrisponde a `2.4.2 <DettaglioPagamento>`
- `FatturaElettronicaPhp\FatturaElettronica\Attachment`
  - `2.5 <Allegati>`
