<?php

namespace App\Session;

class Session implements SessionInterface
{
  public function __construct()
  {
    $this->start();
  }

  public function start(): void
  {
    session_start();
  }

  public function set(string $key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public function get(string $key, $default)
  {
    return $_SESSION[$key] ?? $default;
  }

  public function destroy(): void
  {
    $_SESSION = [];
    session_destroy();
  }
}
