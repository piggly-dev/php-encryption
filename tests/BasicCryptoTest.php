<?php
namespace Piggly\Tests\Security;

use PHPUnit\Framework\TestCase;
use Piggly\Security\BasicCrypto;

class BasicCryptoTest extends TestCase
{
	protected function setUp () 
	{ $this->resetKeys(); }

	/** @test */
	public function isDecrypting ()
	{
		$encrypted = BasicCrypto::encrypt('thisismysecretmessage');
		$decrypted = BasicCrypto::decrypt($encrypted);

		$this->assertEquals('thisismysecretmessage', $decrypted);
	}

	/** @test */
	public function wrongKeyWhileDecrypt ()
	{
		$encrypted = BasicCrypto::encrypt('thisismysecretmessage');
		$this->resetKeys();
		$decrypted = BasicCrypto::decrypt($encrypted);

		$this->assertFalse($decrypted);
	}

	protected function resetKeys ()
	{
		$keys = BasicCrypto::createKeys();

		BasicCrypto
			::setKeys(
				// secret key
				$keys['secret_key']
			);
	}
}