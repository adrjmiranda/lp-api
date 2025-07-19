<?php

namespace LpApi\Services;

use LpApi\Helpers\App;

class ReCAPTCHAService
{
  private function verify(object $response): bool
  {
    $score = (float) App::env("RECAPTCHA_SCORE");
    return $response->success && $response->score >= $score;
  }

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