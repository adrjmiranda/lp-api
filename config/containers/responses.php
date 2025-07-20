<?php

use LpApi\Helpers\ApiResponse;

$container->set(ApiResponse::class, function (): ApiResponse {
  $response = new \Slim\Psr7\Response();
  $apiResponse = new ApiResponse($response);
  return $apiResponse;
});