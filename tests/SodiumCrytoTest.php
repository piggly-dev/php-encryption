<?php
namespace Piggly\Tests\Security;

use PHPUnit\Framework\TestCase;
use Piggly\Security\SodiumCrypto;

class SodiumCryptoTest extends TestCase
{
	protected function setUp () 
	{ $this->resetKeys(); }

	/** @test */
	public function isDecrypting ()
	{
		$encrypted = SodiumCrypto::encrypt('thisismysecretmessage');
		$decrypted = SodiumCrypto::decrypt($encrypted);
 
		$this->assertEquals('thisismysecretmessage', $decrypted);
	}

	/** @test */
	public function isSign ()
	{
		$signedMessage  = SodiumCrypto::sign('iamsigned');
		$validSignature = SodiumCrypto::checkSignature($signedMessage);
		
		$this->assertEquals('iamsigned', $validSignature);
	}

	/** @test */
	public function wrongKeyWhileDecrypt ()
	{
		$encrypted = SodiumCrypto::encrypt('thisismysecretmessage');
		$this->resetKeys();
		$decrypted = SodiumCrypto::decrypt($encrypted);

		$this->assertFalse($decrypted);
	}

	/** @test */
	public function wrongKeyWhileSigned ()
	{
		$signedMessage  = SodiumCrypto::sign('iamsigned');
		$this->resetKeys();
		$validSignature = SodiumCrypto::checkSignature($signedMessage);
 
		$this->assertFalse($validSignature);
	}

	protected function resetKeys ()
	{
		$keys = SodiumCrypto::createKeys();

		SodiumCrypto
			::setKeys(
				// pub
				$keys['pub_key'],
				// private
				$keys['priv_key'],
				// pub signature
				$keys['signature_pub_key'],
				// private signature
				$keys['signature_priv_key']
			);
	}
}