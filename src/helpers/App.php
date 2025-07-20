<?php

namespace LpApi\Helpers;

/**
 * Class App
 *
 * Provides utility methods for the application such as environment variable access,
 * root path retrieval, development mode checking, and input sanitization.
 */
class App
{
  /**
   * Gets the value of an environment variable or returns a default value if not set.
   *
   * @param mixed $key The environment variable key.
   * @param mixed|null $default The default value to return if the environment variable is not set.
   * @return mixed The environment variable value or the default value.
   */
  public static function env($key, $default = null): mixed
  {
    return $_ENV[$key] ?? $default;
  }

  /**
   * Returns the root directory path of the project.
   *
   * @return string The absolute path to the project root directory.
   */
  public static function rootPath(): string
  {
    return dirname(dirname(dirname(__FILE__)));
  }

  /**
   * Checks if the application is running in development mode.
   *
   * @return bool True if the application mode is 'development', false otherwise.
   */
  public static function isDev(): bool
  {
    return self::env("APP_MODE", "production") === "development";
  }

  /**
   * Sanitizes an array of input data by trimming strings, stripping HTML tags,
   * and escaping special characters. Recursively processes nested arrays.
   *
   * @param array $data The raw input data.
   * @return array The sanitized input data.
   */
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
