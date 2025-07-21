<?php

use LpApi\Helpers\ApiResponse;
use LpApi\Helpers\App;
use LpApi\Middlewares\CorsMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/config/container.php";

$envNames = [
  "settings",
  "mail",
  "recaptcha",
  "db",
  "lgpd"
];
$envFiles = array_map(fn(string $name): string => ".env.{$name}", $envNames);
foreach ($envFiles as $file) {
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, $file);
  $dotenv->load();
}

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->add(CorsMiddleware::class);

$errorMiddleware = $app->addErrorMiddleware(App::isDev(), true, App::isDev());

$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($app): Response {
  $response = $app->getResponseFactory()->createResponse();
  $apiResponse = new ApiResponse($response);

  return $apiResponse->send("The requested route was not found", 404);
});

$errorMiddleware->setErrorHandler(HttpMethodNotAllowedException::class, function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($app): Response {
  $response = $app->getResponseFactory()->createResponse();
  $apiResponse = new ApiResponse($response);

  return $apiResponse->send("HTTP method not allowed for this route", 405);
});

