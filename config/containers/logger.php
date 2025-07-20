<?php

use LpApi\Helpers\App;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

$container->set(LoggerInterface::class, function (): Logger {
  $logger = new Logger("app");
  $logDir = App::rootPath() . "/logs";
  $logPath = "{$logDir}/app.log";

  if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
  }

  $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
  return $logger;
});