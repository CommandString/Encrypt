<?php

namespace cmdstr\encrypt;

use Exception;
use InvalidArgumentException;

class encryption {
    /**
     * Passphrase key - must at least a 32 character alphanumeric string
     * 
     * @property string $passphrase
     */
    private string $passphrase;

    /**
     * Encryption method
     * 
     * @property string $encryptionMethod
     */
    private string $encryptionMethod;

    /**
     * @param string $passphrase
     * @param string $encryptionMethod
     */
    public function __construct(string $passphrase, string $encryptionMethod) 
    {
        if (!extension_loaded('openssl')) {
            throw new Exception('The openssl extension is not installed or enabled.');
        }

        if (strlen($passphrase) < 31) {
            throw new InvalidArgumentException("Passphrase must be at least a 32 character string");
        }

        if (!in_array(strtolower($encryptionMethod), openssl_get_cipher_methods())) {
            throw new InvalidArgumentException("Encryption method doesn't exist. Use var_dump(openssl_get_cipher_methods()); to view all available methods.");
        }

        $this->passphrase = $passphrase;

        $this->encryptionMethod = $encryptionMethod;
    }

    /**
     * Encrypt Value
     * 
     * @param string $name
     * @param string $value
     * @param int $hoursValid
     * 
     * @return string
     */
    public function encrypt(string|int $data):string
    {
        $alphabet = [
            ...range(0, 9),
            ...range('a', 'z'),
            ...range('A', 'Z')
        ];
    
        $length = openssl_cipher_iv_length($this->encryptionMethod);
        $bytes = openssl_random_pseudo_bytes($length);
        $iv = '';

        foreach (str_split($bytes) as $byte) {
            $offset = hexdec(bin2hex($byte)) % count($alphabet);
            $iv .= $alphabet[$offset];
        }

        $encryptedString = openssl_encrypt($data, $this->encryptionMethod, $this->passphrase, 0, $iv);
        
        return "$iv:$encryptedString";
    }

    /**
     * Decrypt data
     * 
     * @param string $data
     * @return string
     * 
     * @return string
     */
    public function decrypt(string $data):string
    {
        $parts = explode(":", $data);

        $iv = $parts[0];
        $encryptedString = $parts[1];

        return openssl_decrypt($encryptedString, $this->encryptionMethod, $this->passphrase, 0, $iv);
    }

}