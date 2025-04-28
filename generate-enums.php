<?php

require __DIR__ . '/vendor/autoload.php';

// generate the enum class for each xsd type
$types = extractValuesFromXSD();
$stubFile = getcwd() . '/stubs/XsdEnum.stub';

foreach ($types as $name => $values) {

    // monovalue type => bool
    if (count($values) <= 1) {
        continue;
    }

    $name = getEnumNames()[$name] ?? $name;
    $destinationFile = getcwd() . '/src/Enums/' . $name . '.php';
    $contents = file_get_contents($stubFile);
    $contents = str_replace('__ENUM_NAME__', $name, $contents);

    $enumValues = "";
    $enumLabels = "";
    foreach ($values as $value => $description) {
        $description = addslashes($description);
        $case = str_replace(".", "_", $value);
        $enumValues .= "\tcase $case = '$value';\n";
        $enumLabels .= "\t\t\tself::$case => '$description',\n";
    }

    $contents = str_replace('__ENUM_VALUES__', $enumValues, $contents);
    $contents = str_replace('__ENUM_LABELS__', $enumLabels, $contents);

    file_put_contents($destinationFile, $contents);
}

function extractValuesFromXSD(): array
{
    $types = [];
    $xmldsigFilename = getcwd() . '/src/Validator/xsd/xmldsig-core-schema.xsd';

    foreach (getXDS() as $xsd) {
        $xsd = preg_replace('/(\bschemaLocation=")[^"]+"/', sprintf('\1%s"', $xmldsigFilename), $xsd);

        $doc = new DOMDocument();
        $doc->loadXML(mb_convert_encoding($xsd, 'utf-8', mb_detect_encoding($xsd)));

        $xpath = '/xs:schema/xs:simpleType';
        $domPath = new DOMXPath($doc);
        $nodes = $domPath->evaluate($xpath);

        /** @var DOMElement $node */
        foreach ($nodes as $node) {
            $xpath = 'xs:restriction/xs:enumeration';

            /** @var DOMNodeList $nodes */
            $nodes = $domPath->evaluate($xpath, $node);
            if ($nodes->count() <= 0) {
                continue;
            }

            $typeName = $node->getAttribute('name');
            $types[$typeName] = $types[$typeName] ?? [];
            foreach ($nodes as $values) {
                $value = $values->getAttribute('value');
                $desc = $value;
                $xpath = 'xs:annotation/xs:documentation';
                $descriptions = $domPath->evaluate($xpath, $values);
                /** @var DOMElement $description */
                foreach ($descriptions as $description) {
                    $desc = $description->textContent;
                }
                $types[$typeName][$value] = $desc;
            }
        }
    }

    return $types;
}

function getXDS(): array
{
    return [
        file_get_contents(getcwd() . '/src/Validator/xsd/pa_1.2.xsd'),
        file_get_contents(getcwd() . '/src/Validator/xsd/semplificata_1.0.xsd'),
    ];
}

function getEnumNames(): array
{
    return [
        'CausalePagamentoType' => 'PaymentReason',
        'SocioUnicoType' => 'AssociateType',
        'TipoCessionePrestazioneType' => 'CancelType',
        'TipoRitenutaType' => 'DeductionType',
        'TipoScontoMaggiorazioneType' => 'DiscountType',
        'CondizioniPagamentoType' => 'PaymentTerm',
        'EsigibilitaIVAType' => 'VatEligibility',
        'FormatoTrasmissioneType' => 'TransmissionFormat',
        'ModalitaPagamentoType' => 'PaymentMethod',
        'NaturaType' => 'VatNature',
        'RegimeFiscaleType' => 'TaxRegime',
        'SoggettoEmittenteType' => 'EmittingSubject',
        'StatoLiquidazioneType' => 'WoundUpType',
        'TipoCassaType' => 'FundType',
        'TipoDocumentoType' => 'DocumentType',
    ];
}
