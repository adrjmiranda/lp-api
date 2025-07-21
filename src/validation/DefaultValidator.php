<?php

namespace LpApi\Validation;

class DefaultValidator implements ValidatorInterface
{
  protected static array $rules = [];

  public static function add(string $field, callable $validation, string $message): void
  {
    self::$rules[$field] = [
      "validation" => $validation,
      "message" => $message,
    ];
  }

  public function validate(array $data): array
  {
    $errors = [];

    foreach (self::$rules as $field => $rule) {
      if (!isset($data[$field]) || !$rule["validation"]($data[$field])) {
        $errors[$field] = $rule["message"];
      }
    }

    return $errors;
  }
}
