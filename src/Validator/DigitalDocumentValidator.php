<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Validator;

use DOMDocument;
use Exception;
use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use function sprintf;

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
        } catch (Exception $e) {
            $isValid = false;
        }

        if (! $isValid) {
            $this->manageErrors();
        }

        return $this;
    }

    /**
     * Thanks to https://github.com/Slamdunk/php-validatore-fattura-elettronica
     * @return string
     */
    protected function getSchema(): string
    {
        $schemaFile = $this->document->isSimplified() ? 'semplificata_1.0.xsd' : 'pa_1.2.1.xsd';
        $xsd = file_get_contents(__DIR__ . '/xsd/' . $schemaFile);
        $xmldsigFilename = __DIR__ . '/xsd/core.xsd';
        $xsd = preg_replace('/(\bschemaLocation=")[^"]+"/', sprintf('\1%s"', $xmldsigFilename), $xsd);

        return $xsd;
    }

    protected function manageErrors(): self
    {
        $this->errors = [];

        $errors = libxml_get_errors();

        /**
         * array [level, code, column, message, file, line]
         */
        foreach ($errors as $error) {
            $errorMessage = $this->parseErrorMessage($error->message);
            if ($errorMessage !== null) {
                $this->errors[$errorMessage['field']] = $errorMessage['message'];
            }
        }

        libxml_clear_errors();

        return $this;
    }

    protected function parseErrorMessage(string $message): ?array
    {
        if (stripos($message, "Namespace prefix") === 0) {
            return null;
        }

        $field = '';
        if (stripos($message, "Element ") === 0) {
            $message = substr($message, strlen("Element "));
            $field = substr($message, 1, stripos($message, ':') - 1);
            $message = substr($message, stripos($message, ':') + 2);
        }

        return [
            'field' => $field,
            'message' => $message,
        ];
    }
}
