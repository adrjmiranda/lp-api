<?php

namespace LpApi\Validation;

interface MailerValidationInterface
{
  public function validate(array $data): void;
}