<?php

namespace LpApi\Middlewares;

use LpApi\Helpers\ApiResponse;
use LpApi\Services\LGPDConsentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

/**
 * Middleware responsible for validating and storing LGPD (Brazilian GDPR) consent.
 * 
 * This middleware checks if the incoming request contains valid user consent
 * before allowing further processing. If consent is missing or invalid, it
 * returns an appropriate response. It also logs each action (consent granted or denied).
 */
class LGPDConsentMiddleware implements MiddlewareInterface
{
  /**
   * Logger instance for recording events and errors.
   *
   * @var LoggerInterface
   */
  private LoggerInterface $logger;

  /**
   * Service responsible for formatting and sending API responses.
   *
   * @var ApiResponse
   */
  private ApiResponse $apiResponse;

  /**
   * Service responsible for handling LGPD consent logic (e.g., storing user consent).
   *
   * @var LGPDConsentService
   */
  private LGPDConsentService $lGPDConsentService;

  /**
   * LGPDConsentMiddleware constructor.
   *
   * @param LoggerInterface $logger Logger instance.
   * @param ApiResponse $apiResponse Response helper service.
   * @param LGPDConsentService $lGPDConsentService Service responsible for managing consent storage.
   */
  public function __construct(LoggerInterface $logger, ApiResponse $apiResponse, LGPDConsentService $lGPDConsentService)
  {
    $this->logger = $logger;
    $this->apiResponse = $apiResponse;
    $this->lGPDConsentService = $lGPDConsentService;
  }

  /**
   * Handles the incoming request, validating LGPD consent before proceeding.
   *
   * @param Request $request Incoming HTTP request.
   * @param RequestHandler $handler Next request handler.
   * @return Response HTTP response.
   */
  public function process(Request $request, RequestHandler $handler): Response
  {
    $data = $request->getParsedBody() ?? [];
    $consent = (bool) ($data["consent"] ?? "");

    try {
      if ($consent !== true) {
        $this->logger->warning("Did not consent to sending data", [
          "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
          "path" => (string) $request->getUri(),
        ]);

        return $this->apiResponse->send("LGPD consent is mandatory", 400);
      }

      if (!$this->lGPDConsentService->storeConsent($data)) {
        return $this->apiResponse->send("Error storing LGPD consent", 500);
      }
    } catch (\Throwable $th) {
      $this->logger->error("Error verifying LGPD consent: " . $th->getMessage(), [
        "exception" => $th,
        "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
        "path" => (string) $request->getUri()
      ]);

      return $this->apiResponse->send("Error verifying LGPD consent", 500);
    }

    $this->logger->info("User authorized sending of data", [
      "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
      "path" => (string) $request->getUri()
    ]);

    return $handler->handle($request);
  }
}
