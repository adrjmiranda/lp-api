<?php

namespace LpApi\Middlewares;

use LpApi\Helpers\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Middleware to handle CORS (Cross-Origin Resource Sharing) headers.
 */
class CorsMiddleware implements MiddlewareInterface
{
  /**
   * Processes an incoming server request and returns a response, 
   * adding CORS headers accordingly.
   * 
   * If the HTTP method is OPTIONS, returns a 204 No Content response with CORS headers.
   * Otherwise, passes the request to the next handler and adds CORS headers to the response.
   *
   * @param Request $request The server request.
   * @param RequestHandler $handler The request handler.
   * @return Response Returns the response with CORS headers.
   */
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
