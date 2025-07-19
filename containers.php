<?php

use DI\Container;
use Latte\Engine;
use Monolog\Logger;
use Latte\Loaders\FileLoader;
use LpApi\Helpers\ApiResponse;
use LpApi\Services\ReCAPTCHAService;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

$container = new Container();
AppFactory::setContainer($container);

$container->set(LoggerInterface::class, function (): Logger {
  $logger = new Logger('app');
  $logDir = App::rootPath() . '/logs';
  $logPath = "{$logDir}/app.log";

  if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
  }

  $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
  return $logger;
});

$container->set(Engine::class, function (): Engine {
  $latte = new Engine();

  $tempDir = App::rootPath() . '/temp/cache';
  $templateDir = App::rootPath() . '/templates';

  if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
  }

  if (!is_dir($templateDir)) {
    mkdir($templateDir, 0777, true);
  }

  $latte->setTempDirectory($tempDir);
  $latte->setLoader(new FileLoader($templateDir));

  return $latte;
});

$container->set(ReCAPTCHAService::class, function (): ReCAPTCHAService {
  $recaptchaService = new ReCAPTCHAService();
  return $recaptchaService;
});

$container->set(ApiResponse::class, function (): ApiResponse {
  $response = new \Slim\Psr7\Response();
  $apiResponse = new ApiResponse($response);
  return $apiResponse;
});