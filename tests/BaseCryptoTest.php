<?php
namespace Piggly\Tests\Security;

use PHPUnit\Framework\TestCase;
use Piggly\Security\BasicCrypto;

class BaseCryptoTest extends TestCase
{
	/** @test */
	public function isUnique ()
	{
		$one = BasicCrypto::unique(32);
		$two = BasicCrypto::unique(32);

		$this->assertNotEquals($one, $two);
	}

	/** @test */
	public function doesNotHasSpecialChars ()
	{
		$one = BasicCrypto::unique(32);
		$one = preg_replace('/[a-z0-9]/i', '', $one);

		$this->assertEmpty($one);
	}

	/** @test */
	public function hasSpecialChars ()
	{
		$one = BasicCrypto::unique(32, true);
		$one = preg_replace('/[a-z0-9]/i', '', $one);

		$this->assertNotEquals($one, '');
	}
}