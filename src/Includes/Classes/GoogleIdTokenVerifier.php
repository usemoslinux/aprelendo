<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class GoogleIdTokenVerifier
{
    private const GOOGLE_CERTS_URL = 'https://www.googleapis.com/oauth2/v1/certs';
    private const GOOGLE_ISSUERS = ['accounts.google.com', 'https://accounts.google.com'];

    private string $client_id;

    /**
     * Constructor
     *
     * @param string $client_id
     * @throws InternalException
     */
    public function __construct(string $client_id)
    {
        $this->client_id = trim($client_id);

        if ($this->client_id === '') {
            throw new InternalException('Google Sign-In is not configured.');
        }
    }

    /**
     * Verifies a Google ID token and returns trusted claims.
     *
     * @param string $id_token
     * @return array{sub: string, email: string, name: string, email_verified: bool, hd: string}
     * @throws UserException
     */
    public function verify(string $id_token): array
    {
        $parts = explode('.', trim($id_token));

        if (count($parts) !== 3) {
            throw new UserException('Invalid Google sign-in response. Please try again.');
        }

        [$encoded_header, $encoded_payload, $encoded_signature] = $parts;

        $header = $this->decodeJsonSegment($encoded_header);
        $payload = $this->decodeJsonSegment($encoded_payload);

        if (($header['alg'] ?? '') !== 'RS256') {
            throw new UserException('Invalid Google sign-in response. Please try again.');
        }

        $key_id = $header['kid'] ?? '';
        if (!is_string($key_id) || $key_id === '') {
            throw new UserException('Invalid Google sign-in response. Please try again.');
        }

        $certificates = $this->fetchCertificates();
        $certificate = $certificates[$key_id] ?? '';

        if (!is_string($certificate) || $certificate === '') {
            throw new UserException('Google sign-in could not be verified. Please try again.');
        }

        $signature = $this->decodeBase64Url($encoded_signature);
        $signed_data = $encoded_header . '.' . $encoded_payload;

        $public_key = openssl_pkey_get_public($certificate);
        if ($public_key === false) {
            throw new UserException('Google sign-in could not be verified. Please try again.');
        }

        if (openssl_verify($signed_data, $signature, $public_key, OPENSSL_ALGO_SHA256) !== 1) {
            throw new UserException('Google sign-in could not be verified. Please try again.');
        }

        $this->validatePayload($payload);

        return [
            'sub' => (string) $payload['sub'],
            'email' => strtolower(trim((string) $payload['email'])),
            'name' => trim((string) ($payload['name'] ?? '')),
            'email_verified' => $this->isTruthy($payload['email_verified'] ?? false),
            'hd' => trim((string) ($payload['hd'] ?? '')),
        ];
    }

    /**
     * Checks whether Google is authoritative for the verified email address.
     *
     * @param array{sub: string, email: string, name: string, email_verified: bool, hd: string} $google_profile
     * @return bool
     */
    public function isEmailAuthoritative(array $google_profile): bool
    {
        if (!$google_profile['email_verified']) {
            return false;
        }

        return str_ends_with($google_profile['email'], '@gmail.com') || $google_profile['hd'] !== '';
    }

    /**
     * Fetches Google's signing certificates.
     *
     * @return array<string, string>
     * @throws UserException
     */
    private function fetchCertificates(): array
    {
        try {
            $json_response = Curl::getUrlContents(self::GOOGLE_CERTS_URL);
        } catch (UserException $e) {
            throw new UserException('Google Sign-In is temporarily unavailable. Please try again.');
        }

        $certificates = json_decode($json_response, true);
        if (!is_array($certificates)) {
            throw new UserException('Google Sign-In is temporarily unavailable. Please try again.');
        }

        return $certificates;
    }

    /**
     * Validates the token payload.
     *
     * @param array<string, mixed> $payload
     * @return void
     * @throws UserException
     */
    private function validatePayload(array $payload): void
    {
        $issuer = (string) ($payload['iss'] ?? '');
        if (!in_array($issuer, self::GOOGLE_ISSUERS, true)) {
            throw new UserException('Google sign-in could not be verified. Please try again.');
        }

        $audience = $payload['aud'] ?? '';
        if (!is_string($audience) || !hash_equals($this->client_id, $audience)) {
            throw new UserException('Google sign-in could not be verified. Please try again.');
        }

        $expires = filter_var($payload['exp'] ?? null, FILTER_VALIDATE_INT);
        if ($expires === false || $expires < time()) {
            throw new UserException('Google sign-in has expired. Please try again.');
        }

        $subject = trim((string) ($payload['sub'] ?? ''));
        if ($subject === '') {
            throw new UserException('Google sign-in could not be verified. Please try again.');
        }

        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UserException('Google sign-in could not be verified. Please try again.');
        }

        if (!$this->isTruthy($payload['email_verified'] ?? false)) {
            throw new UserException('Google did not provide a verified email address.');
        }
    }

    /**
     * Decodes a JWT JSON segment.
     *
     * @param string $segment
     * @return array<string, mixed>
     * @throws UserException
     */
    private function decodeJsonSegment(string $segment): array
    {
        $decoded_segment = $this->decodeBase64Url($segment);
        $decoded_json = json_decode($decoded_segment, true);

        if (!is_array($decoded_json)) {
            throw new UserException('Invalid Google sign-in response. Please try again.');
        }

        return $decoded_json;
    }

    /**
     * Decodes a base64url-encoded JWT segment.
     *
     * @param string $value
     * @return string
     * @throws UserException
     */
    private function decodeBase64Url(string $value): string
    {
        $padding_length = (4 - strlen($value) % 4) % 4;
        $normalized_value = strtr($value, '-_', '+/') . str_repeat('=', $padding_length);
        $decoded_value = base64_decode($normalized_value, true);

        if ($decoded_value === false) {
            throw new UserException('Invalid Google sign-in response. Please try again.');
        }

        return $decoded_value;
    }

    /**
     * Normalizes boolean-like Google claims.
     *
     * @param mixed $value
     * @return bool
     */
    private function isTruthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return strtolower($value) === 'true';
        }

        return $value === 1;
    }
}
