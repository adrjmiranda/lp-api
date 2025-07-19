<?php

namespace LpApi\Helpers;

use Slim\Psr7\Response as Response;

class ApiResponse
{
  private Response $response;

  public function __construct(Response $response)
  {
    $this->response = $response;
  }

  private function payload(string $message, array $data = []): string
  {
    $json = json_encode([
      "message" => $message,
      "data" => $data
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
      return '{"message":"Failed to encode JSON"}';
    }
    return $json;
  }

  public function send(string $message, int $status = 200, array $data = []): Response
  {
    $this->response->getBody()->write($this->payload($message, $data));
    return $this->response
      ->withHeader("Content-Type", "application/json")
      ->withStatus($status);
  }
}