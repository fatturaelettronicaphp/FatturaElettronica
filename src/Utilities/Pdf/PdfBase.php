<?php

namespace Weble\FatturaElettronica\Utilities\Pdf;

use \FPDF;
use Weble\FatturaElettronica\Utilities\Pdf\GenericPdfTemplate;

/**
 * Estende la libreria vendor FPDF introducendo il metodo NbLines, che permette di calcolare l'altezza di una MultiCell prima che venga stampata nel PDF.<br>
 * Utilizzare questa classe FPDF per effettuare le produzioni dei PDF nell'applicazione.<br>
 * Introduce inoltre alcuni eventi:
 *        onConstruct - richiamato al costruttore della Pdf
 *        afterConstruct - richiamato
 */
class PdfBase extends FPDF
{
    /**
     * PDF value for DPI
     * @var string
     */
    const DPI = 96;

    /**
     * Conversion value from millimeters to inches.
     * @var float
     */
    const MM_IN_INCH = 25.4;


    /**
     * Pdf handler class
     * @var \Weble\FatturaElettronica\Utilities\Pdf\GenericPdfTemplate
     */
    protected $Handler;


    /**
     * Extends \FPDF class. It introduces the new method NbLines() and an event that can define some operations to perform during FPDF class construction.
     *
     * @param \Weble\FatturaElettronica\Utilities\Pdf\GenericPdfTemplate $handler
     * @param string $orientation
     * @param string $unit
     * @param string $size
     */
    public function __construct (GenericPdfTemplate $handler, $orientation = null, $unit = null, $size = null)
    {
        $this->Handler = $handler;

        if (empty($orientation)) {
            $orientation = 'P';
        }

        if (empty($unit)) {
            $unit = 'mm';
        }

        if (empty($size)) {
            $size = 'A4';
        }

        parent::__construct($orientation, $unit, $size);
    }


    /**
     * Builds the PDF Header using Handler's Header() method
     */
    public function Header ()
    {
        $this->Handler->header();
    }


    /**
     * Builds the PDF Header using Handler's Header() method
     */
    public function Footer ()
    {
        $this->Handler->footer();
    }


    /**
     * Computes the number of lines the text inside a MultiCell of width w will take.<br>
     * The method returns the NUMBER of LINES of text, not the final height, which is to be calculated depending on the line height the text will have.
     *
     * @param int $w The width of the MultiCell
     * @param string $txt The text that will be written into the MultiCell
     *
     * @return int
     */
    public function NbLines ($w, $txt)
    {
        $cw =& $this->CurrentFont['cw'];

        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }

        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);

        if (($nb > 0) and ($s[$nb - 1] == "\n")) {
            $nb--;
        }

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;

        while ($i < $nb) {
            $c = $s[$i];

            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;

                continue;
            }

            if ($c == ' ') {
                $sep = $i;
            }

            $l += $cw[$c];

            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }

                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }

        return $nl;
    }


    /**
     * Converts "pixel" value to millimeters.
     *
     * @param int $pixels
     *
     * @return int
     */
    public function PixelsToMm ($pixels)
    {
        return round($pixels * self::MM_IN_INCH / self::DPI, 0);
    }


    /**
     * Converts millimeters value to pixels.
     *
     * @param int $mm
     *
     * @return int
     */
    public function MmToPixels ($mm)
    {
        return round($mm * self::DPI / self::MM_IN_INCH, 0);
    }

}
