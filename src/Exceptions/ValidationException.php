<?php

declare(strict_types=1);

namespace Recharge\Exceptions;

/**
 * Exception thrown when request data validation fails (client-side)
 *
 * This is thrown BEFORE making an API request when data doesn't meet
 * the SDK's validation rules. Helps catch errors early and reduce API calls.
 */
class ValidationException extends RechargeException
{
    /**
     * @param string $message Error message
     * @param array<string, string> $errors Field-specific validation errors (field => message)
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = 'Validation failed',
        private array $errors = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get all validation errors
     *
     * @return array<string, string> Field-specific errors (field => message)
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if a specific field has an error
     *
     * @param string $field Field name
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Get error for a specific field
     *
     * @param string $field Field name
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}
