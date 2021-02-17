<?php
use Piggly\Security\BasicCrypto;

// Chaves
$keys = BasicCrypto::createKeys();
// <- salve $keys['secret_key'] em um arquivo e leia-o posteriormente em uma constante, ambiente ou afins...

// Inicialize as chaves
BasicCrypto::setKeys( $keys['secret_key'] );

// ...
// Mais tarde no código
// ...
$encrypted = BasicCrypto::encrypt('this is my secret message');
$decrypted = BasicCrypto::decrypt($encrypted);

if ( $descrypted !== false )
{ /** O conteúdo foi descriptografado */ }