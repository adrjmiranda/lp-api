<?php

function env($key, $default = null): mixed
{
  return $_ENV[$key] ?? $default;
}

function rootPath(): string
{
  return dirname(__FILE__);
}

function isDev(): bool
{
  return env("APP_MODE", "production") === "development";
}

function payload(string $message, array $data = []): string
{
  $json = json_encode([
    "message" => $message,
    "data" => $data
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  if ($json === false) {
    return '{"message":"Failed to encode JSON"}';
  }
  return $json;
}
