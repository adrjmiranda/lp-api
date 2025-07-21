<?php

use LpApi\Services\LoggerService;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

$container->set(LoggerInterface::class, function (): Logger {
  $logger = LoggerService::set("app");
  return $logger;
});