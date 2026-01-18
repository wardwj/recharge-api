<?php

declare(strict_types=1);

namespace Recharge\Support;

/**
 * Webhook Validator
 *
 * Validates incoming Recharge webhook requests using HMAC-SHA256 signature verification.
 * Ensures that webhook requests are authentic and have not been tampered with.
 *
 * The validation process:
 * 1. Extracts the `X-Recharge-Hmac-Sha256` header from the request
 * 2. Calculates HMAC-SHA256 using: sha256(client_secret + request_body)
 *    Note: client_secret must be concatenated BEFORE request_body
 * 3. Compares the calculated digest with the received signature using hash_equals() for timing-safe comparison
 *
 * Important:
 * - The API Client Secret is different from your API token
 * - You can find the Client Secret in Recharge merchant portal: Tools and apps → API tokens → [Your Token]
 * - The request body must be in exact JSON string format (even one space difference will fail)
 *
 * @see https://docs.getrecharge.com/docs/webhooks-overview#validating-webhooks
 */
final class WebhookValidator
{
    /**
     * Header name for webhook signature
     */
    private const SIGNATURE_HEADER = 'X-Recharge-Hmac-Sha256';

    /**
     * Validate webhook signature
     *
     * Validates that the incoming webhook request is authentic by comparing
     * the HMAC-SHA256 signature in the request header with a calculated signature.
     *
     * @param string $clientSecret Your Recharge API Client Secret (not the API token)
     * @param string $requestBody Raw request body as JSON string (must be exact format)
     * @param string $receivedSignature The signature from X-Recharge-Hmac-Sha256 header
     * @return bool True if signature is valid, false otherwise
     */
    public static function isValid(string $clientSecret, string $requestBody, string $receivedSignature): bool
    {
        // Calculate expected signature: sha256(client_secret + request_body)
        // Important: client_secret must come BEFORE request_body
        $calculatedSignature = hash('sha256', $clientSecret . $requestBody);

        // Use hash_equals() for timing-safe comparison to prevent timing attacks
        return hash_equals($calculatedSignature, $receivedSignature);
    }

    /**
     * Validate webhook from request data
     *
     * Convenience method that extracts the signature from headers and validates.
     * Useful when working with HTTP request objects or raw header arrays.
     *
     * @param string $clientSecret Your Recharge API Client Secret
     * @param string $requestBody Raw request body as JSON string
     * @param array<string, string>|array<string, array<string>> $headers Request headers
     *                                                                    Can be flat array or array with multiple values per header
     * @return bool True if signature is valid
     * @throws \InvalidArgumentException If signature header is missing
     */
    public static function validateFromHeaders(
        string $clientSecret,
        string $requestBody,
        array $headers
    ): bool {
        $signature = self::extractSignature($headers);

        return self::isValid($clientSecret, $requestBody, $signature);
    }

    /**
     * Extract signature from request headers
     *
     * Handles both flat header arrays and arrays with multiple values per header.
     * Header names are case-insensitive.
     *
     * @param array<string, string>|array<string, array<string>> $headers Request headers
     * @return string The signature value
     * @throws \InvalidArgumentException If signature header is missing
     */
    public static function extractSignature(array $headers): string
    {
        // Normalize header keys to lowercase for case-insensitive lookup
        $normalizedHeaders = [];

        foreach ($headers as $key => $value) {
            $normalizedKey = strtolower((string) $key);

            // Handle arrays of header values (take first value)
            if (is_array($value)) {
                $normalizedHeaders[$normalizedKey] = $value[0] ?? '';
            } else {
                $normalizedHeaders[$normalizedKey] = (string) $value;
            }
        }

        $headerKey = strtolower(self::SIGNATURE_HEADER);

        if (!isset($normalizedHeaders[$headerKey]) || $normalizedHeaders[$headerKey] === '') {
            throw new \InvalidArgumentException(
                sprintf('Missing required webhook signature header: %s', self::SIGNATURE_HEADER)
            );
        }

        return $normalizedHeaders[$headerKey];
    }

    /**
     * Validate webhook signature from PSR-7 ServerRequestInterface
     *
     * Convenience method for validating webhooks from PSR-7 request objects.
     *
     * @param string $clientSecret Your Recharge API Client Secret
     * @param \Psr\Http\Message\ServerRequestInterface $request PSR-7 request object
     * @return bool True if signature is valid
     * @throws \InvalidArgumentException If signature header is missing
     */
    public static function validateFromPsr7(
        string $clientSecret,
        \Psr\Http\Message\ServerRequestInterface $request
    ): bool {
        $requestBody = (string) $request->getBody();
        $signature = $request->getHeaderLine(self::SIGNATURE_HEADER);

        if ($signature === '') {
            throw new \InvalidArgumentException(
                sprintf('Missing required webhook signature header: %s', self::SIGNATURE_HEADER)
            );
        }

        return self::isValid($clientSecret, $requestBody, $signature);
    }
}
