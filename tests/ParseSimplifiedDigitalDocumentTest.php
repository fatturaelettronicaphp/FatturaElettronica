<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use FatturaElettronicaPhp\FatturaElettronica\Contracts\DigitalDocumentInterface;
use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use PHPUnit\Framework\TestCase;

class ParseSimplifiedDigitalDocumentTest extends TestCase
{
    /**
     * @test
     * @dataProvider listOfInvoices
     */
    public function can_read_simplified_invoices(string $filePath)
    {
        $eDocument = DigitalDocument::parseFrom($filePath);
        $this->assertTrue($eDocument instanceof DigitalDocumentInterface);
        $this->assertTrue($eDocument->isSimplified());
        $this->assertTrue($eDocument->isValid(), $filePath . ": " .  json_encode($eDocument->validate()->errors()));
    }

    public function listOfInvoices(): array
    {
        $files = array_map(function ($file) {
            return __DIR__ . '/fixtures/semplificata/' . $file;
        }, array_diff(scandir(__DIR__ . '/fixtures/semplificata'), [
            '.',
            '..',
        ]));

        $keys = array_map(function ($file) {
            return basename($file);
        }, $files);

        $data = [];
        foreach ($keys as $index => $key) {
            $data[$key] = [$files[$index]];
        }

        return $data;
    }
}
