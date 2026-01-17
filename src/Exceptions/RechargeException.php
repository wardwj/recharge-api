<?php

declare(strict_types=1);

namespace Recharge\Exceptions;

use Exception;

/**
 * Base exception for all Recharge SDK exceptions
 *
 * All custom exceptions extend from this class, allowing users to catch
 * any SDK-related exception with a single catch block.
 */
class RechargeException extends Exception
{
}
