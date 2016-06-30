<?php
  class Database extends Redis {
    public $database;
    public function __construct() {
      global $database;
      $redisPassword = "put your redis password here";
      $database = new Redis();
      $database->connect("127.0.0.1", 6379);
      $database->auth($redisPassword);
      $database->select(15); # the last db in the list is our web db
    }
    final public function getDatabase() {
      global $database;
      return $database;
    }
  }
  $db = new Database();
  $db = $db->getDatabase();
