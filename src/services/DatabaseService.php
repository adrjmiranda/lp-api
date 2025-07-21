<?php

namespace LpApi\Services;

use LpApi\Helpers\App;
use Medoo\Medoo;

/**
 * Class responsible for managing the singleton instance of the Medoo database connection.
 * 
 * This class provides a centralized point to retrieve and reset the database connection instance,
 * using the configuration defined in the `config/db.php` file.
 */
class DatabaseService
{
  /**
   * The singleton instance of the database connection.
   *
   * @var Medoo|null
   */
  private static ?Medoo $instance = null;

  /**
   * Private constructor to prevent external instantiation.
   */
  private function __construct()
  {
  }

  /**
   * Retrieves the singleton instance of the database connection.
   * 
   * If the instance has not been created yet, this method loads the database configuration
   * from the `config/db.php` file and initializes the connection.
   *
   * @return Medoo The database connection instance.
   */
  public static function getInstance(): Medoo
  {
    if (self::$instance === null) {
      $configs = require_once App::rootPath() . "/config/db.php";
      self::$instance = new Medoo($configs);
    }

    return self::$instance;
  }

  /**
   * Resets the database connection instance.
   * 
   * This method can be used in tests or scenarios where a new connection needs to be forced.
   *
   * @return void
   */
  public static function reset(): void
  {
    self::$instance = null;
  }
}
