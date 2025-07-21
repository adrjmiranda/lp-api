<?php

namespace LpApi\Middlewares;

use LpApi\Helpers\ApiResponse;
use LpApi\Helpers\App;
use LpApi\Validation\MailerValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

/**
 * MailerSanitizeMiddleware
 *
 * Middleware responsible for sanitizing and validating incoming mailer-related requests.
 * 
 * This middleware parses the JSON body of the request, sanitizes the input,
 * and validates it using the MailerValidator class. If validation fails,
 * it logs the error and returns a standardized API response.
 *
 * If validation passes, the sanitized data is injected back into the request
 * and passed down the middleware chain.
 *
 * @package LpApi\Middlewares
 */
class MailerSanitizeMiddleware implements MiddlewareInterface
{
  /**
   * PSR-3 compatible logger instance.
   *
   * @var LoggerInterface
   */
  private LoggerInterface $logger;

  /**
   * Helper for sending standardized API responses.
   *
   * @var ApiResponse
   */
  private ApiResponse $apiResponse;

  /**
   * Validator for mailer-specific data input.
   *
   * @var MailerValidator
   */
  private MailerValidator $mailerValidator;

  /**
   * Constructor.
   *
   * @param LoggerInterface $logger          PSR-compliant logger.
   * @param ApiResponse     $apiResponse     API response helper.
   * @param MailerValidator $mailerValidator Validator for incoming request data.
   */
  public function __construct(LoggerInterface $logger, ApiResponse $apiResponse, MailerValidator $mailerValidator)
  {
    $this->logger = $logger;
    $this->apiResponse = $apiResponse;
    $this->mailerValidator = $mailerValidator;
  }

  /**
   * Process an incoming server request.
   *
   * This method reads the raw body of the request, decodes and sanitizes the JSON input,
   * then validates the data. If any validation errors are found, a 400 response is returned.
   * Otherwise, the sanitized and validated data is injected back into the request and
   * passed to the next middleware or handler.
   *
   * @param Request        $request PSR-7 request instance.
   * @param RequestHandler $handler PSR-15 request handler.
   *
   * @return Response PSR-7 response.
   */
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
