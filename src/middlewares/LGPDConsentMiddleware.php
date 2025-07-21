<?php

namespace LpApi\Middlewares;

use LpApi\Helpers\ApiResponse;
use LpApi\Helpers\App;
use LpApi\Services\LGPDConsentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

class LGPDConsentMiddleware implements MiddlewareInterface
{
  private LoggerInterface $logger;

  private ApiResponse $apiResponse;

  private LGPDConsentService $lGPDConsentService;


  public function __construct(LoggerInterface $logger, ApiResponse $apiResponse, LGPDConsentService $lGPDConsentService)
  {
    $this->logger = $logger;
    $this->apiResponse = $apiResponse;
    $this->lGPDConsentService = $lGPDConsentService;
  }

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