# Criptografia sem complicação

[![Latest Version on Packagist](https://img.shields.io/packagist/v/piggly/php-encryption.svg?style=flat-square)](https://packagist.org/packages/piggly/php-encryption) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE) 

Essa biblioteca foi criada para facilitar e simplificar o processo de criptografia de dados. O intuito dela é prover uma interface simples para conduzir diferentes tipos de criptografia conforme a necessidade.

Toda class de criptografia deve extender a classe `BaseCrypto` e conter o método `createKeys()` que retorna uma matriz de chaves em `array` para auxiliar o processo de criação de chaves segura para o método de criptografia.

Existe, no momento, dois métodos disponíveis:

* `BasicCrypto` criptografa dados de uma forma simples utilizando as funções nativas `openssl_encrypt` e `openssl_decrypt`. **Requer a extensão openssl** do PHP.
* `SolidumCrypto` criptografa e assina dados utilizando a criptografia de última geração **LibSodium**. **Requer a extensão sodium** do PHP *(veja mais detalhes em instalação)*.

> Se você apreciar a função desta biblioteca e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

## Instalação

Essa biblioteca pode ser instalada via **Composer** com o comando `composer require piggly/php-encryption`. Recomendamos que, após a instalação, execute o comando `composer check-platform-reqs` para verificar se você possui as extensões necessárias para utilizar um ou vários métodos de criptografia.

### Libsodium

> Instale de acordo com o seu sistema operacional. Os procedimentos são diferentes para Linux, Mac e Windows.

A biblioteca `Libsodium` não é nativa do PHP. Deve ser instalada manualmente para que a classe `SolidumCryto` opere corretamente. Para isso, no terminado do seu servidor, execute o seguinte comando `sudo apt-get install libsodium-dev`.

Depois, instale o gerenciador de extensões **PECL** do PHP ([veja aqui](https://www.php.net/manual/pt_BR/install.pecl.php)) com o seguinte comando `sudo apt-get install php-pear`. E, por fim, instale a extenção `libsodium` para o PHP com o comando `sudo pecl install -f libsodium` e ative-a no `php.ini` tanto na versão `cli` quanto na versão `fpm`.

> Conheça mais detalhes da Libsodium [aqui](https://github.com/jedisct1/libsodium-php).

## Como utilizar?

### Criptografia básica com OpenSSL

Utilize a classe `BasicCrypto` para criar operações básicas de criptografia. Para começar sempre gere uma chave segura e salve-a em um ambiente com `.env` ou similares. Sempre grave em um arquivo que esteja protegido contra leitura de terceiros.

Um jeito simples de gerar uma chave aleatória é utilizando a função `BasicCrypto::createKeys()`. A função irá retornar um `array` com a chave `secret_key`. Depois disso, inicialize a classe com o método `BasicCrypto::setKeys()` com a `secret_key` e utilize os métodos `BasicCrypto::encrypt()` e `BasicCrypto::descrypt()` para criptografar. Veja:

```php
// Chaves
$keys = BasicCrypto::createKeys();
// <- salve $keys['secret_key'] em um arquivo e leia-o posteriormente em uma constante, ambiente ou afins...

// Inicialize as chaves
BasicCrypto::setKeys( SECRET_KEY );

// ...
// Mais tarde no código
// ...
$encrypted = BasicCrypto::encrypt('this is my secret message');
$decrypted = BasicCrypto::decrypt($encrypted);

if ( $descrypted !== false )
{ /** O conteúdo foi descriptografado */ }
```

### Criptografia com Sodium

**Sodium** é uma biblioteca moderna projetada para operações de segurança. Ela é multi plataforma e multi linguagem. Seu objetivo é fornercer as principais operações para construção de ferramentas criptográficas de alto nível.

Uma das maiores vantagens, além de construir uma criptografia segura, é que a biblioteca é portável. Isso significa que você pode criptografar uma mensagem com o PHP e, se desejar, descriptografar com NodeJs, Python e afins.

Utilize a classe `SodiumCrypto` para criar operações básicas de criptografia e assinatura. Para começar sempre gere uma sequência de chaves seguras e salve-as em um ambiente com `.env` ou similares. Sempre grave em um arquivo que esteja protegido contra leitura de terceiros.

**ATENÇÃO!** As chaves para trabalhar com `Sodium` precisam ser geradas pelos algoritmos da biblioteca. Por tanto, **sempre** utilize o método `SodiumCrypto::createKeys()`. O método irá retornar um `array` com as seguintes chaves:

Chave | Descrição
--- | ---
`pub_key` | Chave pública para criptografia.
`priv_key` | Chave privada para criptografia.
`signature_pub_key` | Chave pública para assinatura.
`signature_priv_key` | Chave privada para assinatura.

Após gerar e salvar as chaves, inicialize a classe com o método `SodiumCrypto::setKeys()` definindo todas as chaves requeridas. Por fim, utilize os métodos `SodiumCrypto::encrypt()` e `SodiumCrypto::descrypt()` para criptografar e os métodos `SodiumCrypto::sign()` e `SodiumCrypto::checkSignature()` para assinar e verificar a assinatura respectivamente. Veja:

```php
// Chaves
$keys = SodiumCrypto::createKeys();
// <- salve todas as chaves em um arquivo e leia-o posteriormente em uma constante, ambiente ou afins...

// Inicialize as chaves
SodiumCrypto::setKeys( 
	PUBLIC_KEY,
	PRIVATE_KEY,
	SIGNATURE_PUBLIC_KEY,
	SIGNATURE_PRIVATE_KEY 
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
```

#### Criptografia != Assinatura

Ao utilizar o método `SodiumCrypto::sign()` a mensagem **NÃO É** criptografada. Neste caso, a mensagem será apenas assinada para garantir que ela não sofra alterações durante o processo de ida e vinda. Mas, ainda é possível criptografá-la:

```php
$signedMessage  = SodiumCrypto::sign('i am signed');
$encrypted = SodiumCrypto::encrypt($signedMessage);
$decrypted = SodiumCrypto::decrypt($encrypted);
$validSignature = SodiumCrypto::checkSignature($decrypted);

if ( $validSignature !== false )
{ /** O conteúdo é autentico */ }
```

## Changelog

Veja o arquivo [CHANGELOG](CHANGELOG.md) para informações sobre todas as mudanças no código.

## Testes de Código

Essa biblioteca utiliza o [PHPUnit](https://phpunit.de/). Realizamos testes com todas as principais classes dessa aplicação.

```
vendor/bin/phpunit
```

## Contribuições

Veja o arquivo [CONTRIBUTING](CONTRIBUTING.md) para informações antes de enviar sua contribuição.

## Segurança

Se você descobrir qualquer issue relacionada a segurança, por favor, envie um e-mail para [dev@piggly.com.br](mailto:dev@piggly.com.br) ao invés de utilizar o rastreador de issues do Github.

## Créditos

- [Caique Araujo](https://github.com/caiquearaujo)
- [Todos os colaboradores](../../contributors)

## Apoie o projeto

**Piggly Studio** é uma agência localizada no Rio de Janeiro, Brasil. Se você apreciar a função desta biblioteca e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

## Licença

MIT License (MIT). Veja [LICENSE](LICENSE) para mais informações.