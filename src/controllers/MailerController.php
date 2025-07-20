<?php

namespace LpApi\Controllers;

use Latte\Engine;
use LpApi\Helpers\ApiResponse;
use LpApi\Helpers\App;
use LpApi\Services\MailerService;
use LpApi\Validation\DefaultMailerValidator;
use LpApi\Validation\ValidationFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

/**
 * Controller responsible for handling email sending through the API.
 * 
 * This controller receives data from the request body, validates the input,
 * renders an HTML email template using Latte, and sends the email using PHPMailer.
 */
class MailerController
{
  /**
   * Handles JSON API responses.
   *
   * @var ApiResponse
   */
  private ApiResponse $apiResponse;

  /**
   * PSR-3 compatible logger.
   *
   * @var LoggerInterface
   */
  private LoggerInterface $logger;

  /**
   * Service for configuring and sending emails.
   *
   * @var MailerService
   */
  private MailerService $mailerService;

  /**
   * Latte template engine instance.
   *
   * @var Engine
   */
  private Engine $latte;

  /**
   * Validator for the default mailer input.
   *
   * @var DefaultMailerValidator
   */
  private DefaultMailerValidator $defaultMailerValidator;

  /**
   * MailerController constructor.
   *
   * @param ApiResponse $apiResponse
   * @param LoggerInterface $logger
   * @param MailerService $mailerService
   * @param Engine $latte
   * @param DefaultMailerValidator $defaultMailerValidator
   */
  public function __construct(
    ApiResponse $apiResponse,
    LoggerInterface $logger,
    MailerService $mailerService,
    Engine $latte,
    DefaultMailerValidator $defaultMailerValidator
  ) {
    $this->apiResponse = $apiResponse;
    $this->logger = $logger;
    $this->mailerService = $mailerService;
    $this->latte = $latte;
    $this->defaultMailerValidator = $defaultMailerValidator;
  }

  /**
   * Handles the request to send an email.
   *
   * Validates input data, renders the email body using a Latte template,
   * sends the email using MailerService, and returns an appropriate response.
   *
   * @param Request $request The HTTP request containing JSON body with template parameters.
   * @param Response $response The HTTP response object to return.
   * @return Response A PSR-7 compliant response with status code and message.
   */
  public function send(Request $request, Response $response): Response
  {
    try {
      $requestBody = (string) $request->getBody();

      $host = App::env("MAILER_HOST");
      $username = App::env("MAILER_USERNAME");
      $secret = App::env("MAILER_SECRET");
      $port = (int) App::env("MAILER_PORT");

      $fromAddress = App::env("MAILER_FROM_ADDRESS");
      $fromName = App::env("MAILER_FROM_NAME");
      $toAddress = App::env("MAILER_TO_ADDRESS");
      $toName = App::env("MAILER_TO_NAME");

      $subject = App::env("MAILER_SUBJECT");
      $templatePath = App::env("MAILER_TEMPLATE");
      $templateParams = json_decode($requestBody, true);
      $templateParams = App::sanitizeInput($templateParams);

      $this->defaultMailerValidator->validate($templateParams);

      $body = $this->latte->renderToString($templatePath, $templateParams);

      $result = $this->mailerService
        ->serverSettings($host, $username, $secret, $port)
        ->recipients($fromAddress, $fromName, [
          $toAddress => $toName
        ])
        ->content($subject, $body, $body)
        ->send();

      if (!$result) {
        $this->logger->warning("E-mail sending failed", [
          "ip" => $request->getServerParams()["REMOTE_ADDR"] ?? "unknown",
          "path" => (string) $request->getUri()
        ]);

        return $this->apiResponse->send("Error sending email", 500);
      }

      return $this->apiResponse->send("Email sent successfully", 200);

    } catch (ValidationFailedException $th) {
      $this->logger->error("Validation failed for passed data", [
        "exception" => $th->getMessage(),
        "trace" => $th->getTraceAsString()
      ]);

      return $this->apiResponse->send($th->getMessage(), 400, $th->getErrors());

    } catch (\Throwable $th) {
      $this->logger->error("Unexpected error sending email", [
        "exception" => $th->getMessage(),
        "trace" => $th->getTraceAsString()
      ]);

      return $this->apiResponse->send("Unexpected server error", 500);
    }
  }
}
