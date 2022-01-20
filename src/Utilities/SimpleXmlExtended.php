<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Utilities;

use SimpleXMLElement;

class SimpleXmlExtended extends SimpleXMLElement
{
    /**
     * Add CDATA content to current element
     *
     * @param string $text
     *
     * @return $this
     */
    public function addCData($text)
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($text));

        return $this;
    }

    /**
     * Pulisce una stringa e la formatta oppurtunamente per un documento xml
     * con codifica UTF-8
     *
     * @param string $text
     *
     * @return string
     */
    public static function sanitizeText(string $text, int $limit = 1000)
    {
        /* Tolgo i caratteri HTML perchè sto scrivendo dentro un XML */
        $text = htmlspecialchars($text);

        /* Elimino i new line e i carriage return */
        $text = preg_replace('/(\r?\n){1,}/', ' ', $text);

        /* Limito il numero massimo di caratteri */
        $text = mb_substr($text, 0, $limit, 'UTF-8');

        // Le stringhe sono tutte definite come {IsBasicLatin}
        // Questo check controlla se il testo fornito rientra in tale range
        // https://stackoverflow.com/questions/53043409/php-string-validation-for-basiclatin-and-1supplement
        // Se non rientra nel range, converte i caratteri in UTF8 non supportati nella loro versione
        // ASCII più simile al carattere specificato.
        if (! preg_match('~^[\x00-\xFF]{1,' . $limit . '}$~u', $text)) {
            $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        }

        /* Sostituisco i doppi spazi con spazi singoli (va fatto solo alla fine in quanto le varie conversioni precedenti potrebbero talvolta generare spazi bianchi, eventualmente creandoli dunque anche doppi) */
        $text = preg_replace("/\s+/", ' ', $text);

        return $text;
    }

    public static function sanitizeFloat(float $amount, $precision = 2): string
    {
        return number_format(round($amount, $precision), $precision, '.', '');
    }
}
