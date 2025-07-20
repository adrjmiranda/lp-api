<?php

namespace LpApi\Services;

use LpApi\Helpers\App;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailerService
{
  private PHPMailer $mail;

  public function __construct()
  {
    $this->mail = new PHPMailer(true);
  }

  public function serverSettings(string $host, string $username, string $secret, int $port): self
  {
    $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
    $this->mail->isSMTP();
    $this->mail->Host = $host;
    $this->mail->SMTPAuth = true;
    $this->mail->Username = $username;
    $this->mail->Password = $secret;
    $this->mail->SMTPSecure = match (App::env("SECURITY")) {
      "SMTPS" => PHPMailer::ENCRYPTION_SMTPS,
      "STARTTLS" => PHPMailer::ENCRYPTION_STARTTLS,
      default => PHPMailer::ENCRYPTION_STARTTLS
    };
    $this->mail->Port = $port;
    $this->mail->CharSet = "UTF-8";

    $this->mail->Timeout = 10;

    return $this;
  }

  public function recipients(string $fromAddress, string $fromName, array $recipientsAddress): self
  {
    $this->mail->setFrom($fromAddress, $fromName);
    foreach ($recipientsAddress as $address => $name) {
      $this->mail->addAddress($address, $name);
    }

    return $this;
  }

  public function attachments(array $files = []): self
  {
    foreach ($files as $name => $path) {
      $this->mail->addAttachment($path, $name);
    }

    return $this;
  }

  public function content(string $subject, string $body, string $altBody, bool $isHTML = true): self
  {
    $this->mail->isHTML($isHTML);
    $this->mail->Subject = $subject;
    $this->mail->Body = $body;
    $this->mail->AltBody = $altBody;

    return $this;
  }

  public function send(): bool
  {
    return $this->mail->send();
  }
}