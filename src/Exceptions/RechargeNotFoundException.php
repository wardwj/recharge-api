<?php

declare(strict_types=1);

namespace Recharge\Exceptions;

/**
 * Exception thrown when a requested resource is not found (404)
 *
 * This exception is thrown when:
 * - The requested resource doesn't exist
 * - The resource ID is invalid
 * - The endpoint path is incorrect
 *
 * @see https://developer.rechargepayments.com/2021-11/responses
 */
class RechargeNotFoundException extends RechargeApiException
{
}
