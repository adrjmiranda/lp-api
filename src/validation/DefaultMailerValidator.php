<?php

namespace LpApi\Validation;

use Respect\Validation\Validator as v;
use LpApi\Validation\ValidationFailedException;

class DefaultMailerValidator
{
  public function validate(array $data): void
  {
    $errors = [];

    if (!isset($data["name"]) || !v::stringType()->notEmpty()->validate($data["name"])) {
      $errors["name"] = "Nome é obrigatório";
    }

    if (!isset($data["email"]) || !v::email()->validate($data["email"])) {
      $errors["email"] = "E-mail inválido";
    }

    if (!empty($errors)) {
      throw new ValidationFailedException("Validation failed", $errors);
    }
  }
}
