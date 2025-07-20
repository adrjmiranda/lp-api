<?php

namespace LpApi\Validation;

use Exception;

class ValidationFailedException extends Exception
{
  private array $errors;

  public function __construct(string $message = "Validation failed", array $errors = [])
  {
    parent::__construct($message);
    $this->errors = $errors;
  }

  public function getErrors(): array
  {
    return $this->errors;
  }
}
