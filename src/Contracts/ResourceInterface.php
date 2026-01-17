<?php

declare(strict_types=1);

namespace Recharge\Contracts;

/**
 * Resource Interface
 *
 * Defines the contract for Recharge API resource classes.
 */
interface ResourceInterface
{
    /**
     * Get the resource endpoint base path
     */
    public function getEndpoint(): string;
}
