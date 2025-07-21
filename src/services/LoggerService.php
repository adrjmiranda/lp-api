<?php

namespace LpApi\Services;

use LpApi\Helpers\App;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerService
{
  public static function set(string $name): Logger
  {
    $logger = new Logger($name);
    $logDir = App::rootPath() . "/logs";
    $logPath = "{$logDir}/{$name}.log";

    if (!is_dir($logDir)) {
      mkdir($logDir, 0777, true);
    }

    $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
    return $logger;
  }
}