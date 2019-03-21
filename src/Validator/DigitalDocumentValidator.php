<?php

namespace Weble\FatturaElettronica\Validator;

use DOMDocument;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInterface;
use Weble\FatturaElettronica\Utilities\Pipeline;
use Weble\FatturaElettronica\Validator\Body\DeductionValidator;
use Weble\FatturaElettronica\Validator\Body\GeneralDataValidator;
use Weble\FatturaElettronica\Validator\Body\LinesValidator;
use Weble\FatturaElettronica\Validator\Body\TotalsValidator;
use Weble\FatturaElettronica\Validator\Header\CustomerValidator;
use Weble\FatturaElettronica\Validator\Header\IntermediaryValidator;
use Weble\FatturaElettronica\Validator\Header\RepresentativeValidator;
use Weble\FatturaElettronica\Validator\Header\SupplierValidator;
use Weble\FatturaElettronica\Validator\Header\TransmissionDataValidator;

class DigitalDocumentValidator
{
    /** @var DigitalDocumentInterface */
    protected $document;

    protected $errors = [];

    public function __construct(DigitalDocumentInterface $document)
    {
        $this->document = $document;
        $this->performValidation();
    }

    public function isValid(): bool
    {
        return count($this->errors) <= 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    protected function performValidation(): self
    {
        libxml_use_internal_errors(true);

        $documentXml = $this->document->serialize();
        $dom = new DOMDocument();
        $dom->loadXML($documentXml->saveXML());
        $xsd = $this->getSchema();

        try {
            $isValid = $dom->schemaValidateSource($xsd);
        } catch (\Exception $e) {
            $isValid = false;
        }

        if (!$isValid) {
            $this->manageErrors();
        }

        return $this;
    }

    protected function getSchema(): string
    {
        $xsd = file_get_contents(dirname(__FILE__) . '/xsd/Schema_del_file_xml_FatturaPA_versione_1.2.1.xsd');
        $xmldsigFilename       = dirname(__FILE__) . '/xsd/core.xsd';
        $xsd = preg_replace('/(\bschemaLocation=")[^"]+"/', \sprintf('\1%s"', $xmldsigFilename), $xsd);

        return $xsd;
    }

    protected function manageErrors(): self
    {
        $this->errors = [];

        $this->errors = libxml_get_errors();

        libxml_clear_errors();

        return $this;
    }

    protected function validateHeader(): self
    {
        $pipeline = new Pipeline();

        $this->errors = $pipeline
            ->send($this->errors)
            ->with($this->document)
            ->usingMethod('validate')
            ->through([
                TransmissionDataValidator::class,
                SupplierValidator::class,
                RepresentativeValidator::class,
                CustomerValidator::class,
                IntermediaryValidator::class
            ])
            ->thenReturn();

        return $this;
    }

    protected function validateBody(DigitalDocumentInstanceInterface $body): self
    {
        $pipeline = new Pipeline();

        $this->errors = $pipeline
            ->send($this->errors)
            ->with($body)
            ->usingMethod('validate')
            ->through([
                GeneralDataValidator::class,
                DeductionValidator::class,
                LinesValidator::class,
                TotalsValidator::class
            ])
            ->thenReturn();

        return $this;
    }
}
