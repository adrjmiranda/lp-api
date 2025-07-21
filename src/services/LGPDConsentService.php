<?php

namespace LpApi\Services;

use LpApi\Helpers\App;
use Medoo\Medoo;
use Monolog\Logger;

/**
 * Service class responsible for handling the storage of LGPD (Brazilian General Data Protection Law) consent data.
 */
class LGPDConsentService
{
  /**
   * The database connection instance.
   * 
   * @var Medoo
   */
  private Medoo $db;

  /**
   * Logger instance for error and event logging.
   * 
   * @var Logger
   */
  private Logger $logger;

  /**
   * Constructor initializes the database connection and logger instances.
   */
  public function __construct()
  {
    $this->db = DatabaseService::getInstance();
    $this->logger = LoggerService::set("db");
  }

  /**
   * Stores the user's LGPD consent data in the database.
   *
   * This method filters the incoming data based on required fields defined in the environment,
   * appends additional metadata such as purpose, date_sent, and IP address,
   * and attempts to insert it into the "users" table.
   * 
   * Any errors during insertion are logged and the method returns false.
   *
   * @param array $data The user data to be stored.
   * @return bool Returns true if data was successfully stored; otherwise false.
   */
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
