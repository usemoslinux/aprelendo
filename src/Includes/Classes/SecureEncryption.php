<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class SecureEncryption
{
    private $encryption_key;

    /**
     * Constructor to initialize the encryption key
     *
     * @param string $encryption_key
     */
    public function __construct(string $encryption_key)
    {
        // Hash the key to make sure it is 256 bits (32 bytes) for AES-256
        $this->encryption_key = substr(hash('sha256', $encryption_key, true), 0, 32);
    } 

    /**
     * Encrypt the given text
     *
     * @param string $text
     * @return void
     */
    public function encrypt(string $text)
    {
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_length);

        $encrypted_text = openssl_encrypt(
            $text,
            'aes-256-cbc',
            $this->encryption_key,
            0,
            $iv
        );

        // Encode IV and encrypted text together in base64
        return base64_encode($iv . $encrypted_text);
    } 

    /**
     * Decrypt the given text
     *
     * @param string $encrypted_data
     * @return void
     */
    public function decrypt(string $encrypted_data)
    {
        $data = base64_decode($encrypted_data);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $iv_length);
        $encrypted_text = substr($data, $iv_length);

        return openssl_decrypt(
            $encrypted_text,
            'aes-256-cbc',
            $this->encryption_key,
            0,
            $iv
        );
    } 
}

