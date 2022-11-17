# Changelog

I cambiamenti essenziali a `FatturaElettronica` verranno documentati qui

## 2.6.9
- Possibilità di aggiungere allegati senza file

## 2.6.8
- Aggiunto il tipo di documento TD28 (grazie @itajackass)

## 2.6.7
- Aggiunto il parsing del riferimento amministrativo

## 2.6.6
- Aggiunte informazioni mancanti nel parsing dei dati di pagamento

## 2.6.5
- Fix decimali con 8 cifre

## 2.6.4
- Fix xml per l'invio allo SDI

## 2.6.3
- Fix `DataDDT` e `NumeroDDT`

## 2.6.1
- Fix Typo in `DatiContratto`

## 2.6.0
- Importante aggiornamento del document writer con supporto a tutti i campi della fattura elettronica, compresi test

## 2.5.2
- Fix della nuova funzionalità di pretty print XML

## 2.5.1
- Possibilità di formattare in "pretty print" gli xml scritti

## 2.5.0
- Aggiornata compatibilità con la versione 1.6.4 della fattura elettronica
- Sistemata compatibilità con `NumeroLicenzaGuida`
- Sistemato `NumeroRiferimentoLinea`

## 2.4.3
- Aggiunto supporto per `NumeroLicenzaGuida` nei dati vettore

## 2.4.2
- Risolto problema con la scrittura di Fatture Elettroniche con più di un allegato

## 2.4.1
- Risolto un errore relativo al `RappresentanteFiscale`

## 2.4.0
- Aggiunto supporto alla codifica CMS

## 2.3.0
- Supporto per multipe righe di beni e servizi nelle fatture elettroniche semplificate.

## 2.2.3

- Fix per Indirizzo Resa

## 2.2.2

- Fix per campi con spazi.

## 2.2.0
- Aggiunta gestione multiple ritenute (grazie @vetarsonr)

## 2.1.0 - 15/11/2021
- Aggiornata compatibilità XSD
- Risolto problema formato email con "-"

## 2.0.0
- Compatibilità con PHP 8.0

## 1.0.0 - 2019-03-28

- Release Iniziale
- Lettura
- Scrittura
- Validazione
