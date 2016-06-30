<?php
  # Base file for other webfiles to import.
  # Instantiates connections, globals and configuration.
  require "/var/www/serverbuilder/assets/php/config.php";
  $basePath = $GLOBALS['basePath'];
  require_once $basePath . "/vendor/autoload.php";
  require $basePath . "/assets/php/functions.php";
  require $basePath . "/assets/php/database.php";
  require $basePath . "/assets/php/renderer.php";

  if ($GLOBALS["debug_mode"]) {
    // Enable warnings/messages/debug stuff for bug tracking
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);
  }
