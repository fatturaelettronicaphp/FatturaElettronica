<?php


namespace Weble\FatturaElettronica\Utilities;


use phpDocumentor\Reflection\Types\Callable_;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Closure;
use SimpleXMLElement;

class Pipeline
{
    /** @var array */
    protected $pipes = [];

    /** @var mixed */
    protected $object;

    /** @var mixed */
    protected $dependency;

    protected $method = 'handle';

    public function send ($object): self
    {
        $this->object = $object;
        return $this;
    }

    public function through ($pipes): self
    {
        $this->pipes = $pipes;
        return $this;
    }

    public function with ($dependency): self
    {
        $this->dependency = $dependency;
        return $this;
    }

    public function then (Closure $destination)
    {
        $this->carry();
        return $destination($this->object);
    }

    public function thenReturn ()
    {
        return $this->then(function ($passable) {
            return $passable;
        });
    }

    public function usingMethod (string $method): self
    {
        $this->method = $method;
        return $this;
    }

    protected function carry ()
    {
        foreach ($this->pipes as $pipe) {
            $handler = new $pipe($this->object);
            $this->object = $handler->{$this->method}($this->dependency);
        }

    }
}