<?php

declare(strict_types=1);

namespace Recharge\Exceptions;

/**
 * Exception thrown when the API returns a validation error (422 Unprocessable Entity)
 *
 * This exception is thrown when the request is well-formed but cannot be processed
 * due to validation errors or missing required information at the API level.
 *
 * Use getResponseBody() or getResponseField('errors') to access detailed validation errors.
 *
 * Note: This is different from client-side ValidationException which is thrown
 * before making the API request.
 *
 * @see https://developer.rechargepayments.com/2021-11/responses
 * @see ValidationException For client-side validation errors
 */
class RechargeValidationException extends RechargeApiException
{
}
