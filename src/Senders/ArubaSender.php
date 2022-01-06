<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Senders;

use JsonException;

class ArubaSender extends AbstractSender
{
	protected const DEMO_AUTH_URL = 'https://demoauth.fatturazioneelettronica.aruba.it';
	protected const DEMO_INVOICE_URL = 'https://demows.fatturazioneelettronica.aruba.it';
	protected const INVOICE_URL = 'https://auth.fatturazioneelettronica.aruba.it';
	protected const AUTH_URL = 'https://ws.fatturazioneelettronica.aruba.it';

	/**
	 * @var string $upload_invoice_url
	 */
	private $upload_invoice_url = '';
	/**
	 * @var string $auth_url
	 */
	private $auth_url = '';
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
	 * @throws JsonException
	 */
	public function send($document)
	{
		$this->getUrlsByEnvironment();
		$authToken = $this->login();
		$url = $this->upload_invoice_url . '/services/invoice/upload';
		$ch = curl_init($url);
		$headers = [
			'Accept: application/json',
			'Content-Type: application/json',
			'charset: UTF8-8',
			"Authorization: Bearer $authToken"
		];

		$data = [
			'dataFile' => base64_encode($document->serialize()->saveXML())
		];
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => json_encode($data, JSON_THROW_ON_ERROR)
		);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		$result = json_decode($result, true, 512, JSON_THROW_ON_ERROR);

		return $result;
	}

	/**
	 * @inheritDoc
	 *
	 * @throws JsonException
	 */
	protected function login()
	{
		$auth_url = $this->auth_url . '/auth/signin';
		$ch = curl_init($auth_url);
		$data = [
			'grant_type' => 'password',
			'username' => $this->username,
			'password' => $this->password,
		];

		curl_setopt($ch, CURLOPT_POST, 1);
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
				'Content-type: application/x-www-form-urlencoded',
			),
			CURLOPT_POSTFIELDS => http_build_query($data)
		);

		curl_setopt_array($ch, $options);

		$result = curl_exec($ch);

		$result = json_decode($result, true, 512, JSON_THROW_ON_ERROR);

		return $result['access_token']??false;
	}

	/**
	 * @return void
	 */
	private function getUrlsByEnvironment()
	{
		switch ($this->environment){
			case $this->isDevelopment():
				$this->auth_url = self::DEMO_AUTH_URL;
				$this->upload_invoice_url = self::DEMO_INVOICE_URL;
				break;
			case $this->isProduction():
				$this->auth_url = self::AUTH_URL;
				$this->upload_invoice_url = self::INVOICE_URL;
				break;
		}
	}
}