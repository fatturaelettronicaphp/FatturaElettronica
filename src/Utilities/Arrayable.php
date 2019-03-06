<?php


namespace Weble\FatturaElettronica\Utilities;


trait Arrayable
{
    public function toArray (): array
    {
        $values = [];

        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $value = $property->getValue($this);

            if ($value instanceof ArrayableInterface) {
                $value = $value->toArray();
            }

            $values[$property->getName()] = $value;
        }

        return $values;
    }
}