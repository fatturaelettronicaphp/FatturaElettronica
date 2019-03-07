<?php


namespace Weble\FatturaElettronica\Utilities;


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