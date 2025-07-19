<?php

use DI\Container;
use Latte\Engine;
use Latte\Loaders\FileLoader;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

require __DIR__ . "/vendor/autoload.php";

$envs = [".env.settings", ".env.mail", ".env.recaptcha"];
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, $envs);
$dotenv->load();

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

$container->set(LoggerInterface::class, function (): Logger {
  $logger = new Logger('app');
  $logDir = __DIR__ . '/logs';
  $logPath = $logDir . '/app.log';

  if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
  }

  $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
  return $logger;
});

$container->set(Engine::class, function (): Engine {
  $latte = new Engine();

  $tempDir = rootPath() . '/temp/cache';
  $templateDir = rootPath() . '/templates';

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

$app->addRoutingMiddleware();

$app->add(function (Request $request, RequestHandler $handler): Response {
  if ($request->getMethod() === "OPTIONS") {
    $response = new \Slim\Psr7\Response();
    return $response
      ->withHeader("Access-Control-Allow-Origin", env("ALLOW_ORIGINS"))
      ->withHeader("Access-Control-Allow-Methods", env("ALLOW_METHODS"))
      ->withHeader("Access-Control-Allow-Headers", "Content-Type, Authorization")
      ->withHeader("Access-Control-Allow-Credentials", "true")
      ->withStatus(204);
  }

  $response = $handler->handle($request);
  return $response
    ->withHeader("Content-Type", "application/json")
    ->withHeader("Access-Control-Allow-Origin", "*")
    ->withHeader("Access-Control-Allow-Methods", env("ALLOW_METHODS"))
    ->withHeader("Access-Control-Allow-Headers", "Content-Type, Authorization")
    ->withHeader("Access-Control-Allow-Credentials", "true");
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($app): Response {
  $response = $app->getResponseFactory()->createResponse();
  $response->getBody()->write(payload("A rota requisitada não foi encontrada."));
  return $response
    ->withHeader("Content-Type", "application/json")
    ->withStatus(404);
});

$errorMiddleware->setErrorHandler(HttpMethodNotAllowedException::class, function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($app): Response {
  $response = $app->getResponseFactory()->createResponse();
  $response->getBody()->write(payload("Método HTTP não permitido para essa rota."));
  return $response
    ->withHeader("Content-Type", "application/json")
    ->withStatus(405);
});

