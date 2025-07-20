<?php

namespace LpApi\Validation;

use Respect\Validation\Exceptions\NestedValidationException;

class ValidationExceptionFormatter
{
  public static function formatMessages(NestedValidationException $e): array
  {
    return $e->getMessages();
  }
}
