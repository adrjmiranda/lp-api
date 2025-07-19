<?php

namespace LpApi\Services;

class ReCAPTCHAService
{
  private function verify(object $response): bool
  {
    $score = (float) env("RECAPTCHA_SCORE");
    return $response->success && $response->score >= $score;
  }

  public function v3(string $token): bool
  {
    $secret = env("RECAPTCHA_SECRET_KEY");
    $url = env("RECAPTCHA_VERIFY_URL");
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