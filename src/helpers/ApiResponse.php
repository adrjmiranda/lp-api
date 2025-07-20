<?php

namespace LpApi\Helpers;

use Slim\Psr7\Response as Response;

/**
 * Class responsible for standardizing and returning JSON responses in the API.
 */
class ApiResponse
{
  /**
   * PSR-7 Response instance used to send the response.
   *
   * @var Response
   */
  private Response $response;

  /**
   * Class constructor.
   *
   * @param Response $response PSR-7 response instance.
   */
  public function __construct(Response $response)
  {
    $this->response = $response;
  }

  /**
   * Generates the JSON payload for the response.
   *
   * @param string $message The main message of the response.
   * @param array $data Optional additional data to include.
   * @return string Returns a JSON encoded string with message and data.
   */
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

  /**
   * Sends a standardized JSON response.
   *
   * @param string $message The main message of the response.
   * @param int $status HTTP status code of the response (default: 200).
   * @param array $data Optional additional data to include.
   * @return Response Returns the PSR-7 response instance with body and headers set.
   */
  public function send(string $message, int $status = 200, array $data = []): Response
  {
    $this->response->getBody()->write($this->payload($message, $data));

    return $this->response
      ->withHeader("Content-Type", "application/json")
      ->withStatus($status);
  }
}
