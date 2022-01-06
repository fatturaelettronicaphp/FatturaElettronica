<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Senders;

use FatturaElettronicaPhp\FatturaElettronica\Exceptions\Senders\EmptyCredentialsException;
use JsonException;
/**
 * Factory class of the service Aruba
 */
class ArubaSender extends AbstractSender
{
    protected const DEMO_AUTH_URL = 'https://demoauth.fatturazioneelettronica.aruba.it';
    protected const DEMO_INVOICE_URL = 'https://demows.fatturazioneelettronica.aruba.it';
    protected const INVOICE_URL = 'https://auth.fatturazioneelettronica.aruba.it';
    protected const AUTH_URL = 'https://ws.fatturazioneelettronica.aruba.it';

    /**
     * @var string $upload_invoice_url
     */
    private $_upload_invoice_url = '';
    /**
     * @var string $auth_url
     */
    private $_auth_url = '';
    /**
     * @var string aruba username
     */
    private $_username;
    /**
     * @var string aruba password
     */
    private $_password;

    public function __construct($username,$password)
    {

        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     * @inheritDoc
     * @throws     JsonException
     */
    public function send($document)
    {
        $this->getUrlsByEnvironment();
        $authToken = $this->login();
        $url = $this->_upload_invoice_url . '/services/invoice/upload';
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
		if(empty($this->_password) || empty($this->_username)) {
			throw new EmptyCredentialsException("Credentials not provided");
		}

        $auth_url = $this->_auth_url . '/auth/signin';
        $ch = curl_init($auth_url);
        $data = [
        'grant_type' => 'password',
        'username' => $this->_username,
        'password' => $this->_password,
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
            $this->_auth_url = self::DEMO_AUTH_URL;
            $this->_upload_invoice_url = self::DEMO_INVOICE_URL;
            break;
        case $this->isProduction():
            $this->_auth_url = self::AUTH_URL;
            $this->_upload_invoice_url = self::INVOICE_URL;
            break;
        }
    }
}