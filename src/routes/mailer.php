<?php

use LpApi\Controllers\MailerController;
use LpApi\Middlewares\ReCAPTCHAMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->group('/mailer', function (RouteCollectorProxy $group): void {
  $group->post('/send', MailerController::class . ":send");
})->add(ReCAPTCHAMiddleware::class);