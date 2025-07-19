<?php

namespace LpApi\Middlewares;

use LpApi\Helpers\ApiResponse;
use LpApi\Services\ReCAPTCHAService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

class ReCAPTCHAMiddleware implements MiddlewareInterface
{
  private LoggerInterface $logger;
  private ReCAPTCHAService $reCAPTCHAService;
  private ApiResponse $apiResponse;

  public function __construct(LoggerInterface $logger, ReCAPTCHAService $reCAPTCHAService, ApiResponse $apiResponse)
  {
    $this->logger = $logger;
    $this->reCAPTCHAService = $reCAPTCHAService;
    $this->apiResponse = $apiResponse;
  }

  public function process(Request $request, RequestHandler $handler): Response
  {
    $body = (string) $request->getBody();
    $input = json_decode($body, true);
    $token = $input["token"] ?? "";

    if (empty($token)) {
      $this->logger->warning("reCAPTCHA token is missing", [
        "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
        "path" => (string) $request->getUri()
      ]);

      return $this->apiResponse->send("Missing reCAPTCHA token", 400);
    }

    try {
      if (!$this->reCAPTCHAService->v3($token)) {
        $this->logger->warning("Invalid reCAPTCHA token", [
          "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
          "path" => (string) $request->getUri(),
          "token" => $token
        ]);

        return $this->apiResponse->send("Invalid reCAPTCHA token", 400);
      }
    } catch (\Throwable $th) {
      $this->logger->error("Error verifying reCAPTCHA token: " . $th->getMessage(), [
        "exception" => $th,
        "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
        "path" => (string) $request->getUri()
      ]);

      return $this->apiResponse->send("Error verifying reCAPTCHA token", 500);
    }

    $this->logger->info("Valid reCAPTCHA token received", [
      "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
      "path" => (string) $request->getUri()
    ]);

    return $handler->handle($request);
  }
}