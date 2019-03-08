<?php


namespace Weble\FatturaElettronica\Parser;


use phpDocumentor\Reflection\Types\Callable_;
use Weble\FatturaElettronica\Contracts\DigitalDocumentInstanceInterface;
use Closure;
use SimpleXMLElement;

class ParserPipeline
{
    /** @var array */
    protected $pipes = [];

    /** @var DigitalDocumentInstanceInterface */
    protected $digitalDocumentInstance;

    /** @var SimpleXMLElement */
    protected $xml;

    public function send (DigitalDocumentInstanceInterface $digitalDocumentInstance): self
    {
        $this->digitalDocumentInstance = $digitalDocumentInstance;
        return $this;
    }

    public function through ($pipes): self
    {
        $this->pipes = $pipes;
        return $this;
    }

   public function with(SimpleXMLElement $xml): self
   {
       $this->xml = $xml;
       return $this;
   }

    public function then (Closure $destination): DigitalDocumentInstanceInterface
    {
        $this->carry();
        return $destination($this->digitalDocumentInstance);
    }

    public function thenReturn (): DigitalDocumentInstanceInterface
    {
        return $this->then(function ($passable) {
            return $passable;
        });
    }

    protected function carry ()
    {
        foreach ($this->pipes as $pipe) {
            $handler = new $pipe($this->digitalDocumentInstance);
            $this->digitalDocumentInstance = $handler->parse($this->xml);
        }

    }
}