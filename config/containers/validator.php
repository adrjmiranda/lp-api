<?php

use LpApi\Validation\DefaultMailerValidator;

$container->set(DefaultMailerValidator::class, function (): DefaultMailerValidator {
  $defaultMailerValidator = new DefaultMailerValidator();
  return $defaultMailerValidator;
});