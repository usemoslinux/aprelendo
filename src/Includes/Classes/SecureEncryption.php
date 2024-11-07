<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
 *
 * This file is part of aprelendo.
 *
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

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
    } // end __construct()

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
    } // end encrypt()

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
    } // end decrypt()
}

