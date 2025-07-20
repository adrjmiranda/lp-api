<?php

use LpApi\Services\MailerService;
use LpApi\Services\ReCAPTCHAService;


$container->set(ReCAPTCHAService::class, function (): ReCAPTCHAService {
  $recaptchaService = new ReCAPTCHAService();
  return $recaptchaService;
});


$container->set(MailerService::class, function (): MailerService {
  $mailerService = new MailerService();
  return $mailerService;
});