
# [cmdstr/encrypt](https://packagist.org/packages/cmdstr/encrypt) - A simpler way to manipulate cookies in PHP #

### Install with Composer using `composer require cmdstr/encrypt` ###

## Requirements ##
- PHP >=8.0
- Basic understanding of PHP OOP
- Composer 2

## Basic Usage ##
```php
require __DIR__"/vendor/autoload.php";
use cmdstr/encrypt/encryption;

#                            v >=32 character string            v Encryption method #
$encryptor = new encryption("MZCdg02STLzrsj05KE3SIL62SSlh2Ij", "AES-256-CTR");

$encryptedString = $encryptor->encrypt("Hello World"); // 2aPpxvxiUc3W3TCK:xJmkuSYDpOIOX9k=
$decryptedString = $encryptor->decrypt($encryptedString"); // Hello World
```

## Comparing cmdstr/encrypt to regular encrypting ##
### cmdstr/encrypt ###
```php
// config.php
require __DIR__"/vendor/autoload.php";
use cmdstr/encrypt/encryption;

$encryptor = new encryption("MZCdg02STLzrsj05KE3SIL62SSlh2Ij", "AES-256-CTR");
// ...

// somepage.php
require __DIR__"/vendor/autoload.php";
use cmdstr/encrypt/encryption;

$var = /* some value that needs encrypted */;
$encryptedVar = $encryptor->encrypt($var);
// ...

// someotherpage.php
require __DIR__"/vendor/autoload.php";
use cmdstr/encrypt/encryption;

$encryptedVar = /* retrieved encryptedVar from somepage.php */;
$decryptedVar = $encryptor->decrypt($encryptedVar);
//...
```
### Regular encrypting ###
```php
// config.php
$encrypt = [
	"passphrase" => "MZCdg02STLzrsj05KE3SIL62SSlh2Ij",
	"method" => "AES-256-CTR"
];

// ...

// somepage.php
$var = /* some value that needs encrypted */;

$alphabet = [
	...range(0, 9),
	...range('a', 'z'),
	...range('A', 'Z')
];
    
$length = openssl_cipher_iv_length($encrypt["method"]);
$bytes = openssl_random_pseudo_bytes($length);
$iv = '';

foreach (str_split($bytes) as $byte) {
	$offset = hexdec(bin2hex($byte)) % count($alphabet);
	$iv .= $alphabet[$offset];
}

$encryptedVar = openssl_encrypt($data, $encrypt["method"], $encrypt["passphrase"], 0, $iv);
// ...

// someotherpage.php
$encryptedVar = /* retrieved encryptedVar from somepage.php */;
$parts = explode(":", $encryptedVar);

$iv = $parts[0];
$encryptedString = $parts[1];

$decryptedVar = openssl_decrypt($encryptedString, $this->encryptionMethod, $this->passphrase, 0, $iv);
// ...
```
