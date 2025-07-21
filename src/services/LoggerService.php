<?php

namespace LpApi\Services;

use LpApi\Helpers\App;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * LoggerService
 * 
 * Provides a simple factory method to create and configure Monolog Logger instances
 * with a dedicated log file based on the given logger name.
 */
class LoggerService
{
  /**
   * Creates and returns a Monolog Logger instance with a file handler.
   * 
   * The log file is created inside the "/logs" directory at the project root,
   * and the logger writes messages with level DEBUG or higher.
   *
   * @param string $name The name identifier for the logger, which also defines the log file name.
   * @return Logger Returns a configured Monolog Logger instance.
   */
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
