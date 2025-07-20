<?php

namespace LpApi\Validation;

use Respect\Validation\Validator as v;
use LpApi\Validation\ValidationFailedException;

class DefaultMailerValidator
{
  private static array $rules = [];

  public static function add(string $field, callable $validation, string $message): void
  {
    self::$rules[$field] = [
      "validation" => $validation,
      "message" => $message,
    ];
  }

  public function validate(array $data): void
  {
    $errors = [];

    foreach (self::$rules as $field => $rule) {
      if (!isset($data[$field]) || !$rule["validation"]($data[$field])) {
        $errors[$field] = $rule["message"];
      }
    }

    if (!empty($errors)) {
      throw new ValidationFailedException("Validation failed", $errors);
    }
  }
}
