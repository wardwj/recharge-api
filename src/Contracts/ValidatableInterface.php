<?php

declare(strict_types=1);

namespace Recharge\Contracts;

use Recharge\Exceptions\ValidationException;

/**
 * Validatable Interface
 *
 * Contract for objects that require validation before use.
 * Request DTOs should implement this to ensure data integrity.
 */
interface ValidatableInterface
{
    /**
     * Validate the data
     *
     * @return bool True if valid
     * @throws ValidationException If validation fails
     */
    public function validate(): bool;

    /**
     * Get validation rules
     *
     * Returns an array of field => rules for documentation and introspection.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array;
}
