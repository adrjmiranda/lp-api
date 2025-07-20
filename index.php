<?php

use LpApi\Helpers\App;
use LpApi\Validation\DefaultMailerValidator;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/bootstrap.php";

// add email rules
DefaultMailerValidator::add("name", fn(mixed $input): bool => v::stringType()->notEmpty()->validate($input), "Nome é obrigatório");
DefaultMailerValidator::add("email", fn(mixed $input): bool => v::email()->validate($input), "E-mail inválido");

// routes
require_once App::rootPath() . "/src/routes/mailer.php";

// run application
$app->run();