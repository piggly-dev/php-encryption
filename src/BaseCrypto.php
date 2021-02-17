<?php
namespace Piggly\Security;

use RuntimeException;

/**
 * Base class to implement a new encryption method.
 * 
 * @package \Piggly\Security
 * @author Caique Araujo <caique@piggly.com.br>
 * @version 1.0.0
 */
abstract class BaseCrypto
{
	/**
	 * An array with all keys.
	 * 
	 * @since 1.0.0
	 * @var array
	 */
	protected static $keys = [];

	/**
	 * Create an array with all keys.
	 * 
	 * @since 1.0.0
	 * @return array
	 */ 
	abstract public static function createKeys () : array;
	
	/**
	 * Create a random unique string.
	 * 
	 * @param int $length
	 * @param bool $specialChars Include special chars
	 * @since 1.0.1
	 * @return string
	 * @see https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
	 */ 
	public static function unique ( 
		int $length = 32, 
		bool $specialChars = false 
	) : string
	{
		$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$keyspace = $specialChars ? $keyspace.'!@#$%&*()+-/=[]{}?' : $keyspace;
		$keyspace = str_shuffle($keyspace);
		$pieces   = [];
		$max      = mb_strlen($keyspace, '8bit') - 1;

		for ($i = 0; $i < $length; ++$i) 
		{ $pieces[] = $keyspace[random_int(0, $max)]; }

		return implode('', $pieces);
	}

	/**
	 * Get a key.
	 * 
	 * @param string $index
	 * @since 1.0.0
	 * @return string
	 * @throws RuntimeException
	 */
	protected static function getKey ( 
		string $index 
	) : string
	{ 
		if ( isset(self::$keys[$index]) )
		{ return self::$keys[$index]; }

		throw new RuntimeException(sprintf('Cannot find `%s` key.', $index));
	}
}