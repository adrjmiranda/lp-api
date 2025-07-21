<?php

namespace LpApi\Middlewares;

use LpApi\Helpers\ApiResponse;
use LpApi\Helpers\App;
use LpApi\Validation\MailerValidator;
use LpApi\Validation\ValidationFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

class MailerSanitizeMiddleware implements MiddlewareInterface
{
  private LoggerInterface $logger;

  private ApiResponse $apiResponse;

  private MailerValidator $mailerValidator;

  public function __construct(LoggerInterface $logger, ApiResponse $apiResponse, MailerValidator $mailerValidator)
  {
    $this->logger = $logger;
    $this->apiResponse = $apiResponse;
    $this->mailerValidator = $mailerValidator;
  }

  public function process(Request $request, RequestHandler $handler): Response
  {
    $body = (string) $request->getBody();
    $data = json_decode($body, true);
    $data = App::sanitizeInput($data);
    $errors = $this->mailerValidator->validate($data);

    if (!empty($errors)) {
      $this->logger->error("Validation failed for passed data", [
        "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
        "path" => (string) $request->getUri()
      ]);

      return $this->apiResponse->send("Validation failed", 400, $errors);
    }

    $request = $request->withParsedBody($data);

    return $handler->handle($request);
  }
}