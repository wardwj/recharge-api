<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * HTTP Status Code enumeration
 *
 * Represents HTTP status codes used by the Recharge API.
 * Based on the official Recharge API response documentation.
 *
 * @see https://developer.rechargepayments.com/2021-11/responses
 * @see https://developer.rechargepayments.com/2021-01/responses
 */
enum HttpStatusCode: int
{
    // Success (2xx)
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;

    // Client Errors (4xx)
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402; // Request Failed (Recharge-specific: parameters valid but operation failed)
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case CONFLICT = 409;
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case UNPROCESSABLE_ENTITY = 422;
    case UPGRADE_REQUIRED = 426; // Version Invalid (Recharge-specific)
    case TOO_MANY_REQUESTS = 429;

    // Server Errors (5xx)
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case SERVICE_UNAVAILABLE = 503;

    /**
     * Check if status code indicates success
     *
     * @return bool True if status is 2xx
     */
    public function isSuccess(): bool
    {
        $code = (int) $this->value;

        return $code >= 200 && $code < 300;
    }

    /**
     * Check if status code indicates a client error
     *
     * @return bool True if status is 4xx
     */
    public function isClientError(): bool
    {
        $code = (int) $this->value;

        return $code >= 400 && $code < 500;
    }

    /**
     * Check if status code indicates a server error
     *
     * @return bool True if status is 5xx
     */
    public function isServerError(): bool
    {
        $code = (int) $this->value;

        return $code >= 500 && $code < 600;
    }

    /**
     * Check if status code indicates an error
     *
     * @return bool True if status is 4xx or 5xx
     */
    public function isError(): bool
    {
        return $this->isClientError() || $this->isServerError();
    }

    /**
     * Try to create from integer status code
     *
     * @param int $code HTTP status code
     * @return self|null Returns null if code doesn't match a known case
     */
    public static function tryFromCode(int $code): ?self
    {
        return self::tryFrom($code);
    }

    /**
     * Create from integer status code with fallback
     *
     * @param int $code HTTP status code
     * @return self Returns the matching enum or INTERNAL_SERVER_ERROR as fallback
     */
    public static function fromCode(int $code): self
    {
        return self::tryFrom($code) ?? self::INTERNAL_SERVER_ERROR;
    }
}
