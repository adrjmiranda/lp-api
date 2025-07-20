<?php

namespace LpApi\Helpers;

class App
{
  public static function env($key, $default = null): mixed
  {
    return $_ENV[$key] ?? $default;
  }

  public static function rootPath(): string
  {
    return dirname(dirname(dirname(__FILE__)));
  }

  public static function isDev(): bool
  {
    return self::env("APP_MODE", "production") === "development";
  }

  public static function sanitizeInput(array $data): array
  {
    $cleanData = [];

    foreach ($data as $key => $value) {
      if (is_array($value)) {
        $cleanData[$key] = self::sanitizeInput($value);
      } elseif (is_string($value)) {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
        $cleanData[$key] = $value;
      } else {
        $cleanData[$key] = $value;
      }
    }

    return $cleanData;
  }

}