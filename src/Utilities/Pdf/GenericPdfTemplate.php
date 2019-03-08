<?php

namespace Weble\FatturaElettronica\Utilities\Pdf;

use Weble\FatturaElettronica\Utilities\Pdf\PdfTemplateInterface;
use Weble\FatturaElettronica\Utilities\Pdf\PdfBase;


/**
 * Classe astratta per la generazione di PDF. Da utilizzare come base di partenza per la creazione di classi che definiscono il contenuto di un PDF.
 */
abstract class GenericPdfTemplate implements PdfTemplateInterface
{
    /**
     * Oggetto PDF
     * @var \Weble\FatturaElettronica\Utilities\Pdf\PdfBase
     */
    protected $Pdf;

    /**
     * Orientamento del foglio PDF.<br>
     * 'P': Portrait (default)<br>
     * 'L': Landscape
     * @var string
     */
    protected $Orientation = 'P';

    /**
     * Unità di misura, es. 'mm' (millimeters, default).
     * @var string
     */
    protected $Unit = 'mm';

    /**
     * Misura del foglio PDF. Default: 'A4'.
     * @var string
     */
    protected $Size = 'A4';


    /**
     * Classe astratta per la generazione di PDF. Da utilizzare come base di partenza per la creazione di classi che generano PDF.
     */
    public final function __construct ()
    {
        $this->Pdf = new PdfBase($this, $this->Orientation, $this->Unit, $this->Size);

        if (method_exists($this, 'onConstruct')) {
            $this->{'onConstruct'}();
        }
    }
}
