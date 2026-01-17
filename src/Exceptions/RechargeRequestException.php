<?php

declare(strict_types=1);

namespace Recharge\Exceptions;

/**
 * Exception thrown when an HTTP request fails at the transport level
 *
 * This wraps PSR-18 client exceptions to avoid leaking implementation details.
 *
 * Common causes:
 * - Network connectivity issues
 * - DNS resolution failures
 * - Connection timeouts
 * - SSL/TLS errors
 */
class RechargeRequestException extends RechargeException
{
}
