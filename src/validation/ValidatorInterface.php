<?php

namespace LpApi\Validation;

/**
 * Interface ValidatorInterface
 * 
 * Defines the contract for validator classes.
 * 
 * Implementing classes must provide a static method to add validation rules
 * and a method to validate data against those rules.
 */
interface ValidatorInterface
{
  /**
   * Adds a validation rule for a specific field.
   * 
   * @param string   $field      The name of the field to validate.
   * @param callable $validation A callable that takes the field value and returns true if valid.
   * @param string   $message    The error message to return if validation fails.
   * 
   * @return void
   */
  public static function add(string $field, callable $validation, string $message): void;

  /**
   * Validates the provided data against all added rules.
   * 
   * @param array $data Associative array of data to validate.
   * 
   * @return array An array of validation errors, empty if validation passes.
   */
  public function validate(array $data): array;
}
