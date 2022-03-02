<?php

namespace App\Config;

use PDO;

class PdoConnection
{
  private PDO $pdoConnection;

  public function init()
  {
    $this->pdoConnection = new PDO(
      "mysql:host=" . $_ENV['DB_HOST'] . ":" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=utf8mb4",
      $_ENV['DB_USER'],
      $_ENV['DB_PASSWORD']
    );

    if ($_ENV['APP_ENV'] === 'dev') {
      $this->pdoConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
  }

  public function getPdoConnection(): PDO
  {
    return $this->pdoConnection;
  }
}
