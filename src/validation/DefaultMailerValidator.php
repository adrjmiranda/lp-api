<?php

namespace LpApi\Validation;

use LpApi\Validation\ValidationFailedException;

/**
 * Class DefaultMailerValidator
 *
 * Provides a flexible validation mechanism for mailer input data.
 * Allows adding validation rules with associated error messages,
 * and validates an array of data against those rules.
 */
class DefaultMailerValidator
{
  /**
   * Array of validation rules.
   * Each rule contains a 'validation' callable and an error 'message'.
   *
   * @var array<string, array{validation: callable, message: string}>
   */
  private static array $rules = [];

  /**
   * Adds a validation rule for a specific field.
   *
   * @param string $field The name of the field to validate.
   * @param callable $validation A callable that receives the field value and returns bool.
   * @param string $message The error message if validation fails.
   * @return void
   */
  public static function add(string $field, callable $validation, string $message): void
  {
    self::$rules[$field] = [
      "validation" => $validation,
      "message" => $message,
    ];
  }

  /**
   * Validates the provided data against all defined rules.
   *
   * @param array<string, mixed> $data The data to validate.
   * @throws ValidationFailedException Throws exception if any validation rule fails.
   * @return void
   */
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
