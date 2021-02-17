<?php
use Piggly\Security\SodiumCrypto;

// Chaves
$keys = SodiumCrypto::createKeys();
// <- salve todas as chaves em um arquivo e leia-o posteriormente em uma constante, ambiente ou afins...

// Inicialize as chaves
SodiumCrypto::setKeys( 
	$keys['pub_key'],
	$keys['priv_key'],
	$keys['signature_pub_key'],
	$keys['signature_priv_key'] 
);

// ...
// Mais tarde no código
// ...
$encrypted = SodiumCrypto::encrypt('this is my secret message');
$decrypted = SodiumCrypto::decrypt($encrypted);

if ( $descrypted !== false )
{ /** O conteúdo foi descriptografado */ }

// Para criação de assinaturas
$signedMessage  = SodiumCrypto::sign('i am signed');
$validSignature = SodiumCrypto::checkSignature($signedMessage);

if ( $validSignature !== false )
{ /** O conteúdo é autentico */ }