<?php
namespace Piggly\Security;

/**
 * Basic encryption by using openssl functions.
 * 
 * @package \Piggly\Security
 * @author Caique Araujo <caique@piggly.com.br>
 * @version 1.0.0
 */
class BasicCrypto extends BaseCrypto
{
	/**
	 * Defines master key to encrypt the data.
	 * 
	 * @param string $secret_key To basic encryption.
	 * @since 1.0.0
	 * @return void
	 */
	public static function setKeys (
		string $secret_key
	) : void
	{ self::$keys['secret_key'] = $secret_key; }

	/**
	 * Create an array with all keys.
	 * 
	 * secret_key => basic key to encrypt
	 * 
	 * @since 1.0.0
	 * @return array
	 * @see https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
	 */ 
	public static function createKeys () : array
	{
		$keyspace = str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*()+-/=[]{}?');
		$pieces   = [];
		$max      = mb_strlen($keyspace, '8bit') - 1;

		for ($i = 0; $i < 64; ++$i) 
		{ $pieces[] = $keyspace[random_int(0, $max)]; }

		return [
			'secret_key' => implode('', $pieces)
		];
	}

	/**
	 * Basic encryption for a string.
	 *
	 * @param string $string String to encrypt.
	 * @param string $method Encryption method.
	 * @param string $hash Hash algorithm.
	 * @since 1.0.0
	 * @return string	String encrypted.
	 */
	public static function encrypt ( 
		string $string, 
		string $method = 'AES-256-CBC', 
		string $hash = 'sha512'  
	) : string
	{
		$skey       = hash ( $hash, self::getKey('secret_key') );
		$siv        = substr( hash( 'sha256', uniqid( openssl_random_pseudo_bytes(16) , true ) ), 0, 16 );
		$ciphertext = base64_encode( openssl_encrypt( $string, $method, $skey, 0, $siv ) );
		$hash       = substr( hash( 'sha512', $ciphertext.$skey ), 0, 32 );

		return $siv . $hash . $ciphertext;
	}

	/**
	 * Basic decryption for a string.
	 *
	 * @param string $string String to encrypt.
	 * @param string $method Encryption method.
	 * @param string $hash Hash algorithm.
	 * @since 1.0.0
	 * @return string|bool String decrypted or FALSE
	 */
	public static function decrypt ( 
		string $string, 
		string $method = 'AES-256-CBC', 
		string $hash = 'sha512' )
	{
		$skey       = hash ( $hash, self::getKey('secret_key') );
		$siv        = substr( $string, 0, 16 );
		$hash       = substr( $string, 16, 32 );
		$ciphertext = substr( $string, 48 );
		$n_hash     = substr( hash( 'sha512', $ciphertext.$skey ), 0, 32 );

		if ( $n_hash !== $hash )
		{ return false; }

		return openssl_decrypt( base64_decode ( $ciphertext ), $method, $skey, 0, $siv );
	}
}