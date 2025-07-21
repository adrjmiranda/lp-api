<?php

namespace LpApi\Validation;

/**
 * DefaultValidator
 * 
 * Implements a basic validation mechanism that allows adding custom validation rules
 * for specific fields and validating an input array against those rules.
 */
class DefaultValidator implements ValidatorInterface
{
  /**
   * Validation rules array.
   * 
   * Each rule contains a callable validation function and an associated error message.
   * Format:
   * [
   *   'fieldName' => [
   *     'validation' => callable, // validation function returning bool
   *     'message' => string       // error message when validation fails
   *   ],
   *   ...
   * ]
   * 
   * @var array<string, array{validation: callable, message: string}>
   */
  protected static array $rules = [];

  /**
   * Adds a validation rule for a specific field.
   * 
   * @param string   $field      The name of the field to validate.
   * @param callable $validation A callable that accepts the field value and returns bool.
   * @param string   $message    The error message to return if validation fails.
   * 
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
   * Validates the given data against the added validation rules.
   * 
   * @param array<string, mixed> $data Associative array of data to validate.
   * 
   * @return array<string, string> Returns an array of error messages indexed by field name.
   *                               Returns an empty array if validation passes.
   */
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
