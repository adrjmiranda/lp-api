<?php

namespace LpApi\Middlewares;

use LpApi\Helpers\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CorsMiddleware implements MiddlewareInterface
{
  public function process(Request $request, RequestHandler $handler): Response
  {
    $allowOrigin = App::env("ALLOW_ORIGINS", "*");
    $allowMethods = App::env("ALLOW_METHODS", "GET, POST, PUT, DELETE, OPTIONS");

    if ($request->getMethod() === "OPTIONS") {
      $response = new \Slim\Psr7\Response();
      return $response
        ->withHeader("Access-Control-Allow-Origin", $allowOrigin)
        ->withHeader("Access-Control-Allow-Methods", $allowMethods)
        ->withHeader("Access-Control-Allow-Headers", "Content-Type, Authorization")
        ->withHeader("Access-Control-Allow-Credentials", "true")
        ->withStatus(204);
    }

    $response = $handler->handle($request);
    return $response
      ->withHeader("Content-Type", "application/json")
      ->withHeader("Access-Control-Allow-Origin", $allowOrigin)
      ->withHeader("Access-Control-Allow-Methods", $allowMethods)
      ->withHeader("Access-Control-Allow-Headers", "Content-Type, Authorization")
      ->withHeader("Access-Control-Allow-Credentials", "true");
  }
}
