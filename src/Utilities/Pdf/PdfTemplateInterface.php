<?php

namespace Weble\FatturaElettronica\Utilities\Pdf;

use  Weble\FatturaElettronica\Utilities\Pdf\GenericPdfTemplate;

/**
 * Interface to be used for PDF handler classes
 */
interface PdfTemplateInterface
{
    /**
     * Builds the PDF Header
     *
     * @return GenericPdfTemplate $pdf
     */
    public function header ();


    /**
     * Builds the PDF Footer
     *
     * @return GenericPdfTemplate $pdf
     */
    public function footer ();
}
