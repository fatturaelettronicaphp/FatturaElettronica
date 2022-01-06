<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Senders;

use FatturaElettronicaPhp\FatturaElettronica\Exceptions\Senders\EmptyCredentialsException;
use JsonException;

/**
 * Factory class of the service Acube
 */
class AcubeSender extends AbstractSender
{
	protected const ENDPOINT_LOGIN = "login_check";
	protected const ENDPOINT_INVOICES = 'invoices';
	/**
	 * @var string $api_url
	 */
	private $api_url = '';
	/**
	 * @var string acube username
	 */
	private $username;
	/**
	 * @var string acube password
	 */
	private $password;

	public function __construct($username,$password)
	{

		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * @inheritDoc
	 * @throws EmptyCredentialsException
	 * @throws JsonException
	 */
	public function send($document)
	{
		$result = false;
		$this->getUrl();
		$loginToken = $this->login();

		if(!empty($loginToken)){

			$ch = curl_init($this->api_url . self::ENDPOINT_INVOICES);
			$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER => array('Content-type: application/xml', 'Authorization: Bearer ' . $loginToken),
				CURLOPT_POSTFIELDS => $document->serialize()->saveXML()
			);

			curl_setopt_array($ch, $options);

			$result = curl_exec($ch);

			$result = json_decode($result, true);
		}
		return $result;
	}

	/**
	 * Return the acube login token
	 * @inheritDoc
	 * @return string
	 * @throws EmptyCredentialsException
	 * @throws JsonException
	 */
	protected function login()
	{

		if(empty($this->password) || empty($this->username)){
			throw new EmptyCredentialsException("Credentials not provided");
		}

		$ch = curl_init($this->api_url . self::ENDPOINT_LOGIN);

		$json_string = array("email" => $this->username, "password" => $this->password);

		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_POSTFIELDS => json_encode($json_string, JSON_THROW_ON_ERROR)
		);

		curl_setopt_array($ch, $options);

		$result = curl_exec($ch);

		$result = json_decode($result, true, 512, JSON_THROW_ON_ERROR);

		return $result["token"]??false;
	}
	
	private function getUrl()
	{
		if($this->isDevelopment()){
			$this->api_url = 'https://api-sandbox.acubeapi.com/';
		}
		if($this->isProduction()){
			$this->api_url = 'https://api.acubeapi.com/';
		}
	}

}