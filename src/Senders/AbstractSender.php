<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Senders;

use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Enums\EnvironmentEnum;
use FatturaElettronicaPhp\FatturaElettronica\Enums\SenderEnvironments;

abstract class AbstractSender
{
    /**
     * The environment where the code is executed:
     *  - development -> test environment
     *  - production -> real environment
     *
     * @var string $environment
     */
    public $environment;
    /**
     * Abstract method to implement the send method
	 * @return mixed
     * @param  DigitalDocument $document the invoice to send
     */
    abstract public function send($document);
    /**
     * Abstract method to implement the auth method
     *
     * @return mixed
     */
    abstract protected function login();

    /**
     * Return the current environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set the environment
     *
     * @param SenderEnvironments $environment the desidered environment
	 * @return void
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Set the environment of the sender to production
     *
     * @return void
     */
    public function setProduction()
    {
        $this->environment = SenderEnvironments::production()->value;
    }
    /**
     * Set the environment of the sender to development
     *
     * @return void
     */
    public function setDevelopment()
    {
        $this->environment = SenderEnvironments::development()->value;
    }

    /**
     * Check if the environment of the sender to development
     *
     * @return bool
     */
    public function isDevelopment()
    {
        return $this->environment === EnvironmentEnum::ENVIRONMENT_DEVELOPMENT;
    }

    /**
     * Check if the environment of the sender to production
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->environment === EnvironmentEnum::ENVIRONMENT_PRODUCTION;
    }
}