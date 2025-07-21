<?php

use LpApi\Services\LGPDConsentService;
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

$container->set(LGPDConsentService::class, function (): LGPDConsentService {
  $lGPDConsentService = new LGPDConsentService();
  return $lGPDConsentService;
});