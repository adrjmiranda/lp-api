<?php

namespace LpApi\Validation;

interface ValidatorInterface
{
  public static function add(string $field, callable $validation, string $message): void;
  public function validate(array $data): array;
}
