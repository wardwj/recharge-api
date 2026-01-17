<?php

declare(strict_types=1);

namespace Recharge\Exceptions;

/**
 * Exception thrown when authentication fails (401 Unauthorized, 403 Forbidden)
 *
 * Common causes:
 * - Invalid or expired API token
 * - Insufficient permissions for the requested resource
 * - IP address not whitelisted
 */
class RechargeAuthenticationException extends RechargeApiException
{
}
