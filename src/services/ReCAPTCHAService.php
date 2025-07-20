<?php

namespace LpApi\Services;

use LpApi\Helpers\App;

/**
 * Classe responsável por verificar tokens do Google ReCAPTCHA v3.
 * 
 * Essa classe realiza uma requisição ao endpoint de verificação do Google ReCAPTCHA,
 * validando se o token recebido possui um `score` mínimo aceitável, definido em
 * `RECAPTCHA_SCORE` no ambiente.
 */
class ReCAPTCHAService
{
  /**
   * Verifica a resposta recebida do Google reCAPTCHA.
   *
   * @param object $response Objeto de resposta retornado pelo Google reCAPTCHA.
   * @return bool Retorna true se a resposta for válida e o score estiver acima do mínimo.
   */
  private function verify(object $response): bool
  {
    $score = (float) App::env("RECAPTCHA_SCORE");
    return $response->success && $response->score >= $score;
  }

  /**
   * Valida um token ReCAPTCHA v3 com o Google.
   *
   * @param string $token Token retornado pelo reCAPTCHA no frontend.
   * @return bool Retorna true se a verificação do token for bem-sucedida.
   */
  public function v3(string $token): bool
  {
    $secret = App::env("RECAPTCHA_SECRET_KEY");
    $url = App::env("RECAPTCHA_VERIFY_URL");

    $data = [
      "secret" => $secret,
      "response" => $token
    ];

    $options = [
      "http" => [
        "method" => "POST",
        "content" => http_build_query($data)
      ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result);

    return $this->verify($response);
  }
}
