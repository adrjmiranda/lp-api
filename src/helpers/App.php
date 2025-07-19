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
}