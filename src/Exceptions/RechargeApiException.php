<?php

namespace Recharge\Exceptions;

/**
 * Exception thrown when the Recharge API returns an error response
 *
 * @package Recharge\Exceptions
 */
class RechargeApiException extends RechargeException
{
    /**
     * @var array|null Response body from the API
     */
    private ?array $responseBody;

    /**
     * RechargeApiException constructor
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param array|null $responseBody Response body from API
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?array $responseBody = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseBody = $responseBody;
    }

    /**
     * Get the response body from the API
     *
     * @return array|null
     */
    public function getResponseBody(): ?array
    {
        return $this->responseBody;
    }
}
