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
	 * @var SenderEnvironments $environment
	 */
	public $environment = null;
	/**
	 * @param DigitalDocument $document
	 * @return bool
	 */
	abstract public function send($document);
	/**
	 * @return mixed
	 */
	abstract protected function login();

	/**
	 * @return SenderEnvironments
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}

	/**
	 * @param SenderEnvironments $environment
	 */
	public function setEnvironment($environment)
	{
		$this->environment = $environment;
	}

	public function setProduction()
	{
		$this->environment = SenderEnvironments::production()->value;
	}

	public function setDevelopment()
	{
		$this->environment = SenderEnvironments::development()->value;
	}

	/**
	 * @return bool
	 */
	public function isDevelopment()
	{
		return $this->environment === EnvironmentEnum::ENVIRONMENT_DEVELOPMENT;
	}

	/**
	 * @return bool
	 */
	public function isProduction()
	{
		return $this->environment === EnvironmentEnum::ENVIRONMENT_PRODUCTION;
	}
}