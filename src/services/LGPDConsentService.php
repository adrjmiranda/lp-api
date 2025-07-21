<?php

namespace LpApi\Services;

use DateTime;
use LpApi\Helpers\App;
use Medoo\Medoo;
use Monolog\Logger;

class LGPDConsentService
{
  private Medoo $db;
  private Logger $logger;

  public function __construct()
  {
    $this->db = DatabaseService::getInstance();
    $this->logger = LoggerService::set("db");
  }

  public function storeConsent(array $data): bool
  {
    try {
      $requiredFields = array_map("trim", explode(",", App::env("LGPD_DB_FIELDS")));
      $specifications = [
        "purpose" => App::env("LGPD_PURPOSE"),
        "date_sent" => date('Y-m-d H:i:s'),
        "ip" => $_SERVER["REMOTE_ADDR"] ?? null
      ];
      $data = array_merge(array_intersect_key($data, array_flip($requiredFields)), $specifications);
      $this->db->insert("users", $data);

      return true;
    } catch (\Throwable $th) {
      $this->logger->error("Error storing LGPD consent", [
        'message' => $th->getMessage(),
        'data' => $data,
        'trace' => $th->getTraceAsString()
      ]);

      return false;
    }
  }
}
