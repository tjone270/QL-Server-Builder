<?php
  // logging function
  function logEvent($event, $logfile)
  {
    if (isset($_SERVER['REMOTE_ADDR'])) { $logString = ("[" . date("d-m-Y H:i:s") . "/" . $_SERVER['REMOTE_ADDR'] . "]: " . $event . "\n"); }
    else { $logString = ("[" . date("d-m-Y H:i:s") . "/" . "Console" . "]: " . $event . "\n"); }
    $logFilePath = ($GLOBALS['basePath'] . "/assets/logs/". $logfile . ".txt");
    file_put_contents($logFilePath, $logString, FILE_APPEND | LOCK_EX);
  }

  // function to safely get $_POST array items/vars without raising an exception
  function safe_getpostvar($postvar)
  {
    if (isset($_POST[$postvar])) {
      return $_POST[$postvar];
    } else {
      return "";
    }
  }

  // function that returns all cvars expected in a post result.
  function getCvars() {
    $predefinedCvars = $GLOBALS["cvars"];
    $cvars = array();
    foreach ($predefinedCvars as $cvar) {
      if (isset($_POST[$cvar])) {
        $cvars[$cvar] = $_POST[$cvar];
      }
    }
    return $cvars;
  }

  // flushes the specified database object, deleting all keys
  function eraseDb($db) {
    $db->flushDb();
  }

  // to set cvars
  function setCvar($cvar, $value, $serverkey, $db) {
    $db->set($serverkey . ":cvar:$cvar", $value);
    $db->sAdd($serverkey . ":cvars", $cvar);
  }

  // set all cvars
  function setAllCvars($serverkey, $db) {
    $cvars = getCvars();
    foreach ($cvars as $cvar => &$value) {
      if ($cvar == "sv_tags") { // add tomtec reference to server tags
        $sponsorTag = ("TomTec " . $GLOBALS["service_name"] . " " . $GLOBALS["service_version"]);
        $tags = explode(",", $value);
        array_push($tags, $sponsorTag);
        $value = implode(",", $tags);
      }
      setCvar($cvar, $value, $serverkey, $db);
    }

    if (getWeaponBitmask()) {
      setCvar("g_startingWeapons", getWeaponBitmask(), $serverkey, $db); // set up weapons
    }
  }

  // function that returns all plugins
  function getPlugins() {
    $predefinedPlugins = $GLOBALS["plugins"];
    $postedPlugins = $_POST["server_plugins"];
    $plugins = array();
    if (isset($postedPlugins)) {
      foreach ($predefinedPlugins as $plugin) {
        if (in_array($plugin, $postedPlugins)) {
          $plugins[] = $plugin;
        }
      }
    }
    return $plugins;
  }

  // sets all plugins to be activated in the db
  function setAllPlugins($serverkey, $db) {
    $plugins = getPlugins();
    if (isset($plugins)) {
      foreach ($plugins as $plugin) {
        $db->sAdd($serverkey . ":plugins", $plugin);
      }
      $db->sAdd($serverkey . ":plugins", "branding"); // always load branding
      $db->sAdd($serverkey . ":plugins", "workshop"); // always load workshop
    }
  }

  // function that returns the weapon bitmask
  function getWeaponBitmask() {
    $bitmask = 0;
    $weapons = safe_getpostvar("g_startingWeapons");
    if ($weapons == "") {
      return false;
    }
    foreach ($weapons as $weapon) {
      if ($weapon == "DEFAULT") {
        return false;
      }
      $bitmask = $bitmask + $weapon;
    }
    return $bitmask;
  }

  // return the selected factory
  function getServerFactory() {
    $submitted = safe_getpostvar("server_factory");
    $pql = safe_getpostvar("server_factory_pql");
    if ($pql == "1") {
      $factory = "pql" . $submitted;
    } else {
      $factory = $submitted;
    }
    return $factory;
  }

  // return the selected map
  function getServerMap() {
    return safe_getpostvar("server_map");
  }

  // function that returns the IP address of a server by location.
  function getServerIp($location) {
    $servers = array(
      "sydney" => "103.18.40.15",
      "adelaide" => "119.252.27.101",
    );
    return $servers[strtolower($location)];
  }

  // function that returns the port of a server by server id.
  function getServerPort($serverId) {
    return $GLOBALS["basePort"] + $serverId;
  }

  // function that returns a free server id.
  function getFreeServerId($location, $db) {
    $maxServers = $GLOBALS["numservers"];
    for ($serverId = 0; $serverId <= $maxServers; $serverId++) {
      $server = "server_" . $serverId;
      $receiving = $db->get($location . ":" . $server . ":receiving");
      if (intval($receiving) == 1) {
        return $serverId;
      }
    }
    if ($serverId == ($maxServers + 1)) {
      return false;
    } else {
      return $serverId;
    }
  }

  // returns true if a post request was made
  function wasRequestPost() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      return true;
    } else {
      return false;
    }
  }

  // returns the server db key
  function getServerKey($location, $serverId) {
    return $location . ":server_" . $serverId;
  }

  // returns the server location
  function getLocation() {
    $location = strtolower($_POST["server_location"]);
    $location = str_replace(" ", "-", $location);
    return $location;
  }

  // activates a server, allowing connections
  function activateServer($serverkey, $db) {
    $db->set($serverkey . ":active", "1");
  }

  // sets up the serverstartup keys
  function setServerStartup($serverkey, $db) {
    $db->set($serverkey . ":map", getServerMap());
    $db->set($serverkey . ":factory", getServerFactory());
  }
