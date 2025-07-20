<?php

namespace LpApi\Validation;

/**
 * Interface MailerValidationInterface
 *
 * Defines a contract for mailer data validation classes.
 */
interface MailerValidationInterface
{
  /**
   * Validates the provided data.
   *
   * @param array<string, mixed> $data The data to validate.
   * @return void
   */
  public function validate(array $data): void;
}
