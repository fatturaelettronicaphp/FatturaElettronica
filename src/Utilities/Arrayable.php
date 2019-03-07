<?php


namespace Weble\FatturaElettronica\Utilities;


use DateTime;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Weble\FatturaElettronica\DigitalDocumentInstance;
use Weble\FatturaElettronica\Enums\DeductionType;
use Weble\FatturaElettronica\Enums\DiscountType;
use Weble\FatturaElettronica\Enums\FundType;
use Weble\FatturaElettronica\Enums\VatNature;

trait Arrayable
{
    public function toArray (): array
    {
        $values = [];

        $properties = (new \ReflectionClass($this))->getProperties();
        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $this->$name;

            if (is_array($value)) {
                foreach ($value as &$v) {
                    if ($v instanceof ArrayableInterface) {
                        $v = $v->toArray();
                    }
                }
            }

            if ($value instanceof ArrayableInterface) {
                $value = $value->toArray();
            }

            $values[$property->getName()] = $value;
        }

        return $values;
    }
}