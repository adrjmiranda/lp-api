<?php

namespace LpApi\Validation;

use Respect\Validation\Exceptions\NestedValidationException;

/**
 * Class ValidationExceptionFormatter
 *
 * Provides helper methods to format validation exception messages.
 */
class ValidationExceptionFormatter
{
  /**
   * Formats the messages from a NestedValidationException.
   *
   * @param NestedValidationException $e The exception to format.
   * @return string[] An array of formatted error messages.
   */
  public static function formatMessages(NestedValidationException $e): array
  {
    return $e->getMessages();
  }
}
