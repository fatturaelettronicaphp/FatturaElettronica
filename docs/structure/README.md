# Struttura del pacchetto Fattura Elettronica

Il pacchetto contiene una struttura di classi costruita sulla base del tracciato XML Fattura Elettronica così come ufficialmente documentato dall'Agenzia delle Entrate: [Documentazione FatturaPA Agenzia delle Entrate](https://www.fatturapa.gov.it/export/fatturazione/it/normativa/f-2.htm) Il riferimento ufficiale utilizzato per la lettura e la scrittura di file XML è: `Rappresentazione tabellare del tracciato FatturaPA versione 1.2.1.xls`.

## Dati enumerabili

L'XML della fattura elettronica prevede diversi dati enumerabili: alcuni sono indicati in apposite tabelle (`RegimeFiscale`, `TipoCassa`, ecc.), altri sono invece riportati nei campi in cui vengono utilizzati (tipo sconto/maggiorazione, quantità di soci, ecc.).

Ognuno di questi dati è tradotto in una apposita classe `Enum` nel namespace `Weble\FatturaElettronica\Enums`.

- Socio unico/più soci: `Weble\FatturaElettronica\Enums\AssociateType`
- Tipo cessione/prestazione: `Weble\FatturaElettronica\Enums\CancelType`
- Tipo ritenuta: `Weble\FatturaElettronica\Enums\DeductionType`
- Tipo sconto/maggiorazione: `Weble\FatturaElettronica\Enums\DiscountType`
- Formato documento: `Weble\FatturaElettronica\Enums\DocumentFormat`
- Tipo documento: `Weble\FatturaElettronica\Enums\DocumentType`
- Individuazione soggetto emittente: `Weble\FatturaElettronica\Enums\EmittingSubject`
- Tipo cassa previdenziale: `Weble\FatturaElettronica\Enums\FundType`
- Metodo di pagamento: `Weble\FatturaElettronica\Enums\PaymentMethod`
- Termini di pagamento: `Weble\FatturaElettronica\Enums\PaymentTerm`
- Codici destinatario di default (cliente estero o codice destinatario B2B/B2C sconosciuto o non presente): `Weble\FatturaElettronica\Enums\RecipientCode`
- Regime fiscale: `Weble\FatturaElettronica\Enums\TaxRegime`
- Formato trasmissione: `Weble\FatturaElettronica\Enums\TransmissionFormat`
- Esigibilità IVA: `Weble\FatturaElettronica\Enums\VatEligibility`
- Tipologia di Natura IVA: `Weble\FatturaElettronica\Enums\VatNature`
- Stato liquidazione: `Weble\FatturaElettronica\Enums\WoundUpType`

## Struttura base

La classe di base da utilizzare è `Weble\FatturaElettronica\DigitalDocument`, che rappresenta un intero file XML (o p7m) di una Fattura Elettronica.

``` php
$eDocument = \Weble\FatturaElettronica\DigitalDocument::parseFrom( $xml );
```

Un'istanza di `Weble\FatturaElettronica\DigitalDocument` contiene essenzialmente le due parti principali di un XML:

- `1 <FatturaElettronicaHeader>`
- `2 <FatturaElettronicaBody>`

### Header

I dati dell'intestazione del documento elettronico sono espressi proprietà della classe `Weble\FatturaElettronica\DigitalDocument` con i relativi metodi le lettura e scrittura (get/set/add).

- `1.1 <DatiTrasmissione>`
  - I dati relativi a questo nodo sono espressi in metodi disponibili nella classe `DigitalDocument`
- `1.2 <CedentePrestatore>` (o Fornitore)
  - Istanza di `Weble\FatturaElettronica\Supplier`
- `1.3 <RappresentanteFiscale>`
  - Istanza di `Weble\FatturaElettronica\Customer`
- `1.4 <CessionarioCommittente>`
  - Istanza di `Weble\FatturaElettronica\BillablePerson`
- `1.5 <TerzoIntermediarioOSoggettoEmittente>`
  - Istanza di `Weble\FatturaElettronica\Intermediary`
- `1.6 <SoggettoEmittente>`
  - Istanza di `Weble\FatturaElettronica\BillablePerson`

La classe `Weble\FatturaElettronica\Address` rappresenta un Indirizzo utilizzato in ognuna delle classi sopra citate.

### Corpo fattura

Ogni file XML può contenere una o più fatture elettroniche grazie alla presenza di uno o più nodi `<FatturaElettronicaBody>`. Quindi l'istanza di `Weble\FatturaElettronica\DigitalDocument` contiene una collezione di `Weble\FatturaElettronica\DigitalDocumentInstance`. Ognuno di essi rappresenta appunto un `<FatturaElettronicaBody>`.

I dati presenti in `2.1.1 <DatiGeneraliDocumento>` sono rappresentati in proprietà disponibili in `Weble\FatturaElettronica\DigitalDocumentInstance`. Alcuni di essi sono collezioni di istanze di altri oggetti che a loro volta contengono i dati previsti dall'XML.

Le linee del corpo fattura (`2.2 <DatiBeniServizi>`) sono rappresentate dalla collezione di istanze di `Weble\FatturaElettronica\Line`.

- `Weble\FatturaElettronica\Fund`
  - `2.1.1.7 <DatiCassaPrevidenziale>`
- `Weble\FatturaElettronica\Discount`, utilizzata per esprimere uno o più sconti (collezione) per i nodi:
  - `2.1.1.8 <ScontoMaggiorazione>`
  - `2.2.1.10 <ScontoMaggiorazione>`
- `Weble\FatturaElettronica\RelatedDocument`
  - `2.1.3 <DatiContratto>`
  - `2.1.4 <DatiConvenzione>`
  - `2.1.5 <DatiRicezione>`
  - `2.1.6 <DatiFattureCollegate>`
- `Weble\FatturaElettronica\ShippingLabel`
  - `2.1.8 <DatiDDT>`
- `Weble\FatturaElettronica\Shipment`
  - `2.1.9 <DatiTrasporto>`
- `Weble\FatturaElettronica\Total`
  - `2.2.2 <DatiRiepilogo>`
- `Weble\FatturaElettronica\PaymentInfo`
  - `2.4 <DatiPagamento>`
  - A sua volta contiene una o più istanze di `Weble\FatturaElettronica\PaymentDetails` che corrisponde a `2.4.2 <DettaglioPagamento>`
- `Weble\FatturaElettronica\Attachment`
  - `2.5 <Allegati>`
