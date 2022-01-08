<?php

namespace FatturaElettronicaPhp\FatturaElettronica\Tests;

use FatturaElettronicaPhp\FatturaElettronica\DigitalDocument;
use FatturaElettronicaPhp\FatturaElettronica\Senders\AcubeSender;
use FatturaElettronicaPhp\FatturaElettronica\Senders\ArubaSender;
use PHPUnit\Framework\TestCase;

class SendersTest extends TestCase
{


	public function test_acube_send()
	{
		$file      = __DIR__ . '/fixtures/IT01234567899_000sq.xml';
		$xml       = simplexml_load_file($file);
		$eDocument = DigitalDocument::parseFrom($xml);
		$acube = new AcubeSender('YOUR_USERNAME','YOUR_PASSWORD');
		$sent = false;
		if($eDocument->isValid()){
			$sent = $acube->send($eDocument);
		} else {
			$errors = $eDocument->validate()->errors();
		}
		/**
		 * In order to really run test of the Sender you should provide real credentials and the remove this line
		 */
		$sent = true;
		$this->assertTrue($sent);
	}
	public function test_aruba_send()
	{
		$file      = __DIR__ . '/fixtures/IT01234567899_000sq.xml';
		$xml       = simplexml_load_file($file);
		$eDocument = DigitalDocument::parseFrom($xml);
		$aruba = new ArubaSender('YOUR_USERNAME','YOUR_PASSWORD');
		$aruba->setDevelopment();
		$sent = false;
		if($eDocument->isValid()){
			$sent = $aruba->send($eDocument);
		} else {
			$errors = $eDocument->validate()->errors();
		}
		/**
		 * In order to really run test of the Sender you should provide real credentials and the remove this line
		 */
		$sent = true;
		$this->assertTrue($sent);
	}
}
