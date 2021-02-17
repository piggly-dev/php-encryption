<?php
namespace Piggly\Security;

/**
 * Encryption and signature using the lib sodium functions.
 * 
 * @package \Piggly\Security
 * @author Caique Araujo <caique@piggly.com.br>
 * @version 1.0.0
 */
class SodiumCrypto extends BaseCrypto
{
	/**
	 * Defines all keys to encrypt and/or sign the data.
	 * 
	 * @param string $pub_key Public key to encrypt.
	 * @param string $priv_key Private key to encrypt.
	 * @param string $signature_pub_key Public key to sign.
	 * @param string $signature_priv_key Private key to sign.
	 * @since 1.0.0
	 * @return void
	 */
	public static function setKeys (
		string $pub_key,
		string $priv_key,
		string $signature_pub_key,
		string $signature_priv_key
	) : void
	{ 
		self::setSodiumKey('pub_key', $pub_key);
		self::setSodiumKey('priv_key', $priv_key);
		self::setSodiumKey('signature_pub_key', $signature_pub_key);
		self::setSodiumKey('signature_priv_key', $signature_priv_key);

		self::setSodiumKey('master_key', sodium_crypto_box_keypair_from_secretkey_and_publickey( 
			self::getSodiumKey('priv_key'), 
			self::getSodiumKey('pub_key')
		));
	} 

	/**
	 * Create an array with all keys.
	 * 
	 * pub_key => To encrypt
	 * priv_key => To decrypt
	 * signature_pub_key => To sign
	 * signature_priv_key => To check signature
	 * 
	 * @since 1.0.0
	 * @return array
	 */ 
	public static function createKeys () : array
	{
		$master = sodium_crypto_box_keypair();
		$signed = sodium_crypto_sign_keypair();

		return [
			'pub_key' => sodium_bin2base64(sodium_crypto_box_publickey($master), \SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING),
			'priv_key' => sodium_bin2base64(sodium_crypto_box_secretkey($master), \SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING),
			'signature_pub_key' => sodium_bin2base64(sodium_crypto_sign_publickey($signed), \SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING), 
			'signature_priv_key' => sodium_bin2base64(sodium_crypto_sign_secretkey($signed), \SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING)
		];
	}

	/**
	 * Sign a message with current signature_priv_key.
	 * 
	 * @param string $message
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function sign ( string $message )
	{
		return sodium_bin2base64( 
			sodium_crypto_sign(
				$message,
				self::getSodiumKey('signature_priv_key')
			),
			\SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
		);
	}
	
	/**
	 * Validates if signed message was signed by signature_priv_key
	 * usign signature_pub_key.
	 * 
	 * @param string $message
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function checkSignature ( string $message )
	{
		return sodium_crypto_sign_open( 
			sodium_base642bin(
				$message, 
				\SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
			),
			self::getSodiumKey('signature_pub_key')
		);
	}

	/**
	 * Encrypt a string $message by using priv_key.
	 *
	 * @param string $message String to encrypt.
	 * @since 1.0.0
	 * @return mixed	String encrypted.
	 */
	public static function encrypt ( string $message )
	{
		return sodium_bin2base64( 
			sodium_crypto_box_seal( 
				$message, 
				self::getSodiumKey('pub_key')
			), 
			\SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING 
		);
	}

	/**
	 * Decrypt a string $message by using pub_key and priv_key.
	 *
	 * @param string $message String to encrypt.
	 * @since 1.0.0
	 * @return mixed String decrypted.
	 */
	public static function decrypt ( string $message )
	{
		return sodium_crypto_box_seal_open( 
			sodium_base642bin( 
				$message, 
				\SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
			), 
			self::getSodiumKeyPair()
		); 
	}

	/**
	 * Set a new sodium key. It will use $value and change
	 * as needed.
	 * 
	 * @param string $index
	 * @param string $value
	 * @since 1.0.0
	 * @return void
	 */
	protected static function setSodiumKey (
		string $index,
		string $value
	) : void
	{ self::$keys['sl_'.$index] = $value; }

	/**
	 * Get a key.
	 * 
	 * @param string $index
	 * @since 1.0.0
	 * @return mixed
	 */
	protected static function getSodiumKey ( 
		string $index 
	)
	{ return self::parseKey(self::getKey('sl_'.$index)); } 

	/**
	 * Get a key usign Libsodium function sodium_base642bin()
	 * which decodes a Base64 string using the given variant.
	 * 
	 * @param string $key
	 * @since 1.0.0
	 * @return mixed
	 */
	protected static function parseKey ( 
		string $key 
	)
	{
		return sodium_base642bin(
			$key,
			\SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
		);
	} 

	/**
	 * Get Libsodium key pair.
	 * 
	 * @since 1.0.0
	 * @return mixed
	 */
	protected static function getSodiumKeyPair ()
	{
		return sodium_crypto_box_keypair_from_secretkey_and_publickey( 
			self::getSodiumKey('priv_key'), 
			self::getSodiumKey('pub_key')
		);
	}
}