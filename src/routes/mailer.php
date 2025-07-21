<?php

use LpApi\Controllers\MailerController;
use LpApi\Middlewares\LGPDConsentMiddleware;
use LpApi\Middlewares\MailerSanitizeMiddleware;
use LpApi\Middlewares\ReCAPTCHAMiddleware;
use Slim\Routing\RouteCollectorProxy;

// $app->group('/mailer', function (RouteCollectorProxy $group): void {
//   $group->post('/send', MailerController::class . ":send");
// });

// $app->group('/mailer', function (RouteCollectorProxy $group): void {
//   $group->post('/send', MailerController::class . ":send");
// })->add(ReCAPTCHAMiddleware::class);

$app->group('/mailer', function (RouteCollectorProxy $group): void {
  $group->post('/send', MailerController::class . ":send");
})->add(LGPDConsentMiddleware::class)
  ->add(MailerSanitizeMiddleware::class);