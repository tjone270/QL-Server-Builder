#! /bin/bash
# This file is part of the Quake Live server implementation by TomTec Solutions. Do not copy or redistribute or link to this file without the emailed consent of Thomas Jones (thomas@tomtecsolutions.com).
# quakestart_serverbuilder.sh - quake live server builder server initialisation script
# created by Thomas Jones on 25/06/16.
# thomas@tomtecsolutions.com

# Defining globally used variables/configuration.
export qMinqlxRedisPassword=$(<~/localConfig-redisPassword.txt)
export qServerLocation=$(<~/localConfig-serverLocation.txt)
export qServerLocationHyphenated=`echo $qServerLocation | sed 's/ /-/g'`
export qPathToStartScript="~/steamcmd/steamapps/common/qlds/run_server_x64_minqlx.sh"

gameport=`expr $1 + 60000`

echo "========== QuakeStart_ServerBuilder.sh has started. =========="
echo "========= $(date) ========="
cd ~/steamcmd/steamapps/common/qlds/baseq3

echo "Removing server instance #$1's homepath..."
rm -rf ~/.quakelive_serverbuilder/$1

echo "Starting server instance #$1..."
exec $qPathToStartScript \
    +set net_strict 1 \
    +set net_port $gameport \
    +set zmq_rcon_enable 0 \
    +set zmq_stats_enable 1 \
    +set qlx_redisPassword "$qMinqlxRedisPassword" \
    +set fs_homepath ~/.quakelive_serverbuilder/$1 \
    +set qlx_owner "76561198213481765" \
    +set sv_location "$qServerLocation" \
    +set sv_identifier $1 \
    +set sv_tags "TomTec ServerBuilder,Unconfigured" \
    +set serverstartup "map campgrounds ffa" \
    +set qlx_plugins "serverbuilder_node"
