<?php

namespace CommandString\Encrypt;

use Exception;
use InvalidArgumentException;


class Encryption {
    private static array $characterCache;
    private string $passphrase;

    /**
     * Encryption method
     * 
     * @var string $encryptionMethod
     */
    private string $encryptionMethod;

    /**
     * @param string $passphrase
     * @param string $encryptionMethod
     *
     * @throws Exception
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
     * @param string|int $data
     *
     * @return string
     * @throws Exception
     */
    public function encrypt(string|int $data):string
    {
        if (!isset(self::$characterCache)) {
            self::$characterCache = [
                ...range(0, 9),
                ...range('a', 'z'),
                ...range('A', 'Z')
            ];
        }

        $length = openssl_cipher_iv_length($this->encryptionMethod);
        $bytes = openssl_random_pseudo_bytes($length);
        $iv = '';

        foreach (str_split($bytes) as $byte) {
            $offset = hexdec(bin2hex($byte)) % count(self::$characterCache);
            $iv .= self::$characterCache[$offset];
        }

        $encryptedString = openssl_encrypt($data, $this->encryptionMethod, $this->passphrase, 0, $iv);

        if ($encryptedString === false) {
            throw new Exception(sprintf("Failed to encrypt a string. Reason: %s", $this->collectErrors()));
        }

        return "$iv:$encryptedString";
    }

    /**
     * Decrypt data
     *
     * @param string $data
     *
     * @return string
     *
     * @throws InvalidArgumentException If data is not encrypted.
     * @throws Exception If decryption fails.
     */
    public function decrypt(string $data) :string
    {
        if (!str_contains($data, ':')) {
            throw new InvalidArgumentException('Failed to decrypt data. Reason: data is not encrypted.');
        }

        list($iv, $encryptedString) = explode(":", $data);

        $result = openssl_decrypt($encryptedString, $this->encryptionMethod, $this->passphrase, 0, $iv);

        if ($result === false) {
            throw new Exception(sprintf("Failed to decrypt data. Reason: %s", $this->collectErrors()));
        }

        return $result;
    }

    private function collectErrors(): string
    {
        $errors = [];

        while ($error = openssl_error_string()) {
            $errors[] = $error;
        }

        return implode('; ', $errors);
    }

    /**
     * Security measures.
     *
     * @return array|null
     */
    public function __debugInfo(): ?array
    {
        return [
            'passphrase' => '********',
            'encryptionMethod' => $this->encryptionMethod
        ];
    }
}
