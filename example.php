<?php

/**
 * Entry point for the application.
 * 
 * This script bootstraps the system, registers validation rules,
 * loads routes, and starts the application.
 */

use LpApi\Helpers\App;
use LpApi\Validation\DefaultMailerValidator;
use Respect\Validation\Validator as v;

// Load application dependencies and setup autoloading
require_once __DIR__ . "/bootstrap.php";

// Register email validation rules using the DefaultMailerValidator

// Rule for 'name' field: must be a non-empty string
DefaultMailerValidator::add(
  "name",
  fn(mixed $input): bool => v::stringType()->notEmpty()->validate($input),
  "Nome é obrigatório"
);

// Rule for 'email' field: must be a valid email address
DefaultMailerValidator::add(
  "email",
  fn(mixed $input): bool => v::email()->validate($input),
  "E-mail inválido"
);

// Load the mailer-related routes
require_once App::rootPath() . "/src/routes/mailer.php";

// Start the application
$app->run();
