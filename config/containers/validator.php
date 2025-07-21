<?php

use LpApi\Validation\MailerValidator;

$container->set(MailerValidator::class, function (): MailerValidator {
  $mailerValidator = new MailerValidator();
  return $mailerValidator;
});