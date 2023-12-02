
# [CommandString/Encrypt](https://packagist.org/packages/commandstring/encrypt) - A simpler way to encrypt data in PHP #

### Install with Composer using `composer require CommandString/Encrypt` ###

## Requirements ##
- PHP >=8.0
- Basic understanding of PHP OOP
- Composer 2

## Basic Usage ##
```php
require __DIR__"/vendor/autoload.php";
use CommandString/Encrypt/Encryption;

#                            v >=32 character string            v Encryption method #
$encryptor = new Encryption("MZCdg02STLzrsj05KE3SIL62SSlh2Ij", "AES-256-CTR");

$encryptedString = $encryptor->encrypt("Hello World"); // 2aPpxvxiUc3W3TCK:xJmkuSYDpOIOX9k=
$decryptedString = $encryptor->decrypt($encryptedString"); // Hello World
```

## Comparing CommandString/Encrypt to regular encrypting ##
### CommandString/Encrypt ###
```php
// config.php
require __DIR__"/vendor/autoload.php";
use CommandString/Encrypt/Encryption;

$encryptor = new Encryption("MZCdg02STLzrsj05KE3SIL62SSlh2Ij", "AES-256-CTR");
// ...

// somepage.php
require_once "config.php";

$var = /* some value that needs encrypted */;
$encryptedVar = $encryptor->encrypt($var);
// ...

// someotherpage.php
require_once "config.php";

$encryptedVar = /* retrieved encryptedVar from somepage.php */;
$decryptedVar = $encryptor->decrypt($encryptedVar);
//...
```
