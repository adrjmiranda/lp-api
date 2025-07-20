<?php

namespace LpApi\Middlewares;

use LpApi\Helpers\ApiResponse;
use LpApi\Services\ReCAPTCHAService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

/**
 * Middleware to verify Google reCAPTCHA tokens in incoming requests.
 */
class ReCAPTCHAMiddleware implements MiddlewareInterface
{
  /**
   * Logger instance to log messages.
   *
   * @var LoggerInterface
   */
  private LoggerInterface $logger;

  /**
   * Service for verifying reCAPTCHA tokens.
   *
   * @var ReCAPTCHAService
   */
  private ReCAPTCHAService $reCAPTCHAService;

  /**
   * ApiResponse helper to return JSON responses.
   *
   * @var ApiResponse
   */
  private ApiResponse $apiResponse;

  /**
   * Constructor to initialize dependencies.
   *
   * @param LoggerInterface $logger Logger instance.
   * @param ReCAPTCHAService $reCAPTCHAService Service for reCAPTCHA verification.
   * @param ApiResponse $apiResponse Helper to send API responses.
   */
  public function __construct(LoggerInterface $logger, ReCAPTCHAService $reCAPTCHAService, ApiResponse $apiResponse)
  {
    $this->logger = $logger;
    $this->reCAPTCHAService = $reCAPTCHAService;
    $this->apiResponse = $apiResponse;
  }

  /**
   * Process the incoming request to validate the reCAPTCHA token.
   * 
   * - Reads the token from the JSON request body.
   * - If token is missing or invalid, logs the issue and returns an error response.
   * - On success, passes control to the next middleware/handler.
   *
   * @param Request $request The incoming server request.
   * @param RequestHandler $handler The request handler.
   * @return Response Returns a PSR-7 response, either error or the next handler's response.
   */
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
