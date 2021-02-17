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