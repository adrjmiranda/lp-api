<?php

namespace LpApi\Services;

use LpApi\Helpers\App;
use Medoo\Medoo;

class DatabaseService
{
  private static ?Medoo $instance = null;

  private function __construct()
  {
  }

  public static function getInstance(): Medoo
  {
    if (self::$instance === null) {
      $configs = require_once App::rootPath() . "/config/db.php";
      self::$instance = new Medoo($configs);
    }

    return self::$instance;
  }

  public static function reset(): void
  {
    self::$instance = null;
  }
}