<?php

namespace LpApi\Validation;

use Exception;

/**
 * Class ValidationFailedException
 *
 * Exception thrown when data validation fails.
 * Contains detailed validation error messages.
 */
class ValidationFailedException extends Exception
{
  /**
   * Array of validation error messages.
   *
   * @var array
   */
  private array $errors;

  /**
   * Constructor for ValidationFailedException.
   *
   * @param string $message The exception message (default: "Validation failed").
   * @param array $errors An array of validation errors.
   */
  public function __construct(string $message = "Validation failed", array $errors = [])
  {
    parent::__construct($message);
    $this->errors = $errors;
  }

  /**
   * Returns the array of validation errors.
   *
   * @return array The validation errors.
   */
  public function getErrors(): array
  {
    return $this->errors;
  }
}
