<?php

namespace LpApi\Helpers;

use Slim\Psr7\Response as Response;

/**
 * Classe responsável por padronizar e retornar respostas em formato JSON na API.
 */
class ApiResponse
{
  /**
   * Instância de Response PSR-7 usada para enviar a resposta.
   *
   * @var Response
   */
  private Response $response;

  /**
   * Construtor da classe.
   *
   * @param Response $response Instância de resposta PSR-7.
   */
  public function __construct(Response $response)
  {
    $this->response = $response;
  }

  /**
   * Gera o payload da resposta em JSON.
   *
   * @param string $message Mensagem principal da resposta.
   * @param array $data Dados adicionais a serem enviados (opcional).
   * @return string Retorna o JSON codificado com a mensagem e os dados.
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
   * Envia uma resposta JSON padronizada.
   *
   * @param string $message Mensagem principal da resposta.
   * @param int $status Código de status HTTP da resposta (padrão: 200).
   * @param array $data Dados adicionais a serem enviados (opcional).
   * @return Response Retorna a instância de resposta PSR-7 com os dados escritos e cabeçalhos definidos.
   */
  public function send(string $message, int $status = 200, array $data = []): Response
  {
    $this->response->getBody()->write($this->payload($message, $data));

    return $this->response
      ->withHeader("Content-Type", "application/json")
      ->withStatus($status);
  }
}
