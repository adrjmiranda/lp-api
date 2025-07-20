<?php

namespace LpApi\Services;

use LpApi\Helpers\App;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Service responsible for configuring and sending emails using PHPMailer.
 */
class MailerService
{
  /**
   * The PHPMailer instance used to send emails.
   *
   * @var PHPMailer
   */
  private PHPMailer $mail;

  /**
   * Initialize a new MailerService instance.
   */
  public function __construct()
  {
    $this->mail = new PHPMailer(true);
  }

  /**
   * Configure the SMTP server settings.
   *
   * @param string $host     The SMTP host.
   * @param string $username The SMTP username.
   * @param string $secret   The SMTP password or secret.
   * @param int    $port     The SMTP port.
   * @return self
   */
  public function serverSettings(string $host, string $username, string $secret, int $port): self
  {
    $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
    $this->mail->isSMTP();
    $this->mail->Host = $host;
    $this->mail->SMTPAuth = true;
    $this->mail->Username = $username;
    $this->mail->Password = $secret;
    $this->mail->SMTPSecure = match (App::env("MAILER_SECURITY")) {
      "SMTPS" => PHPMailer::ENCRYPTION_SMTPS,
      "STARTTLS" => PHPMailer::ENCRYPTION_STARTTLS,
      default => PHPMailer::ENCRYPTION_STARTTLS
    };
    $this->mail->Port = $port;
    $this->mail->CharSet = "UTF-8";
    $this->mail->Timeout = 10;

    return $this;
  }

  /**
   * Set the sender and recipients for the email.
   *
   * @param string $fromAddress       Sender email address.
   * @param string $fromName          Sender name.
   * @param array  $recipientsAddress Associative array of recipients [email => name].
   * @return self
   */
  public function recipients(string $fromAddress, string $fromName, array $recipientsAddress): self
  {
    $this->mail->setFrom($fromAddress, $fromName);
    foreach ($recipientsAddress as $address => $name) {
      $this->mail->addAddress($address, $name);
    }

    return $this;
  }

  /**
   * Attach files to the email.
   *
   * @param array $files Associative array of files [filename => path].
   * @return self
   */
  public function attachments(array $files = []): self
  {
    foreach ($files as $name => $path) {
      $this->mail->addAttachment($path, $name);
    }

    return $this;
  }

  /**
   * Set the content of the email.
   *
   * @param string $subject  The subject of the email.
   * @param string $body     The HTML body of the email.
   * @param string $altBody  The plain text alternative body.
   * @param bool   $isHTML   Whether the email body is HTML (default: true).
   * @return self
   */
  public function content(string $subject, string $body, string $altBody, bool $isHTML = true): self
  {
    $this->mail->isHTML($isHTML);
    $this->mail->Subject = $subject;
    $this->mail->Body = $body;
    $this->mail->AltBody = $altBody;

    return $this;
  }

  /**
   * Send the configured email.
   *
   * @return bool True if the email was sent successfully, false otherwise.
   */
  public function send(): bool
  {
    return $this->mail->send();
  }
}
