<?php
  require_once "assets/php/base.php";

  $flushenabled = false;

  $pageVars = array(
    "flushenabled" => $flushenabled,
  );

  if (wasRequestPost()) {
    if (isset($_POST["flushdb"])) {
      eraseDb($db);
    }

    $location = getLocation();
    $serverId = getFreeServerId($location, $db);
    if (!is_int($serverId)) {
      $pageVars = array_merge($pageVars, array(
        "message" => ucfirst($location) . " has run out of available server nodes. Please try another location, or try again later.",
      ));
      render_page("index.tmpl", $pageVars);
      exit();
    }
    $serverkey = getServerKey($location, $serverId);

    $pageVars = array_merge($pageVars, array(
      "message" => "Your server is now up and running, click Join Server to connect to your server, run <b>!getinfo</b> to test values went across correctly. Note that the first person to connect to your server will automatically recieve moderator permissions, make sure that person is you!",
      "joinlink" => true,
      "net_ip" => getServerIp($location),
      "net_port" => getServerPort($serverId),
      "map_selected" => safe_getpostvar("server_map"),
    ));

    setAllCvars($serverkey, $db);
    setAllPlugins($serverkey, $db);
    setServerStartup($serverkey, $db);
    activateServer($serverkey, $db);
    
    logEvent("Server (" . safe_getpostvar("sv_hostname") . ") created in " . $location . ": /connect " . getServerIp($location) . ":" . getServerPort($serverId), "servers");
  }
  render_page("index.tmpl", $pageVars);
